<?php

namespace App\Filament\Resources\Tenants\Tables;

use App\Actions\Landlord\RetryTenantProvisioningAction;
use App\Enums\Landlord\TenantDatabaseStatus;
use App\Enums\Landlord\TenantStatus;
use App\Jobs\Tenant\ProvisionTenantJob;
use App\Models\Landlord\Tenant;
use App\Services\Tenant\TenantHealthCheckService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class TenantsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Institution')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (Tenant $record): ?string => $record->legal_name),
                TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->copyable()
                    ->badge()
                    ->color('gray'),
                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('primaryDomain.domain')
                    ->label('Primary Domain')
                    ->icon('heroicon-o-globe-alt')
                    ->color('primary')
                    ->default('-')
                    ->searchable(),
                TextColumn::make('database.status')
                    ->label('Database')
                    ->badge()
                    ->color(fn (TenantDatabaseStatus|string|null $state): string => match ($state instanceof TenantDatabaseStatus ? $state : TenantDatabaseStatus::tryFrom((string) $state)) {
                        TenantDatabaseStatus::READY => 'success',
                        TenantDatabaseStatus::PROVISIONING, TenantDatabaseStatus::CREATING, TenantDatabaseStatus::MIGRATING => 'info',
                        TenantDatabaseStatus::FAILED, TenantDatabaseStatus::UNREACHABLE => 'danger',
                        TenantDatabaseStatus::MAINTENANCE, TenantDatabaseStatus::DECOMMISSIONED => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state): string => $state instanceof TenantDatabaseStatus ? ($state->getLabel() ?? ucfirst($state->value)) : ($state ? ucfirst((string) $state) : 'Unassigned'))
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('timezone')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('country')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(TenantStatus::class),
                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('openPortal')
                    ->label('Buka Portal')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('info')
                    ->url(fn (Tenant $record): string => url('/tenant?tenant='.($record->slug ?: $record->code)))
                    ->openUrlInNewTab(),
                ViewAction::make(),
                EditAction::make(),
                Action::make('provision')
                    ->label('Provision')
                    ->icon('heroicon-o-cpu-chip')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn (Tenant $record): bool => $record->status === TenantStatus::PENDING || $record->database?->status === TenantDatabaseStatus::FAILED)
                    ->action(function (Tenant $record): void {
                        ProvisionTenantJob::dispatch((string) $record->getKey());
                        Notification::make()
                            ->title('Provisioning Dispatched')
                            ->info()
                            ->send();
                    }),
                Action::make('retryProvisioning')
                    ->label('Retry Provisioning')
                    ->icon('heroicon-o-arrow-path')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Tenant $record): bool => $record->database?->status === TenantDatabaseStatus::FAILED)
                    ->action(function (Tenant $record, RetryTenantProvisioningAction $retryAction): void {
                        $job = $retryAction->execute($record);
                        Notification::make()
                            ->title('Provisioning Retry Dispatched')
                            ->body(sprintf('Attempt #%d dispatched to queue.', $job->attempts))
                            ->warning()
                            ->send();
                    }),
                Action::make('healthCheck')
                    ->label('Health Check')
                    ->icon('heroicon-o-heart')
                    ->color('info')
                    ->visible(fn (Tenant $record): bool => $record->database?->status === TenantDatabaseStatus::READY)
                    ->action(function (Tenant $record, TenantHealthCheckService $healthCheckService): void {
                        $database = $record->database;
                        if (! $database) {
                            Notification::make()
                                ->title('No Database Configured')
                                ->danger()
                                ->send();

                            return;
                        }

                        $result = $healthCheckService->check($database);
                        if ($result['healthy']) {
                            Notification::make()
                                ->title('Database Healthy')
                                ->body(sprintf('Latency: %.2f ms (Critical tables present)', $result['latency_ms']))
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Health Check Failed')
                                ->body($result['error'] ?? 'Unknown error')
                                ->danger()
                                ->send();
                        }
                    }),
                Action::make('activate')
                    ->label('Activate')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Tenant $record): bool => $record->status !== TenantStatus::ACTIVE)
                    ->action(function (Tenant $record): void {
                        // Invariant #1: ACTIVE requires database to be READY
                        if ($record->database?->status !== TenantDatabaseStatus::READY) {
                            Notification::make()
                                ->title('Activation Blocked')
                                ->body('Tenant database must be READY and healthy before activating.')
                                ->danger()
                                ->send();

                            return;
                        }

                        $record->update(['status' => TenantStatus::ACTIVE]);
                        Notification::make()
                            ->title('Tenant Activated')
                            ->success()
                            ->send();
                    }),
                Action::make('suspend')
                    ->label('Suspend')
                    ->icon('heroicon-o-pause-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Tenant $record): bool => $record->status === TenantStatus::ACTIVE)
                    ->action(function (Tenant $record): void {
                        $record->update(['status' => TenantStatus::SUSPENDED]);
                        Notification::make()
                            ->title('Tenant Suspended')
                            ->warning()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
