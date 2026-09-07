<?php

namespace App\Filament\Resources\TenantDatabases\Tables;

use App\Enums\Landlord\TenantDatabaseStatus;
use App\Models\Landlord\TenantDatabase;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TenantDatabasesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label('Tenant')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('database')
                    ->label('Database')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-circle-stack'),
                TextColumn::make('host_and_port')
                    ->label('Host:Port')
                    ->state(fn (TenantDatabase $record): string => "{$record->host}:{$record->port}")
                    ->toggleable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (TenantDatabaseStatus|string|null $state): string => match ($state instanceof TenantDatabaseStatus ? $state : TenantDatabaseStatus::tryFrom((string) $state)) {
                        TenantDatabaseStatus::READY => 'success',
                        TenantDatabaseStatus::CREATING, TenantDatabaseStatus::MIGRATING => 'warning',
                        TenantDatabaseStatus::FAILED => 'danger',
                        TenantDatabaseStatus::MAINTENANCE => 'gray',
                        default => 'secondary',
                    })
                    ->sortable(),
                TextColumn::make('schema_version')
                    ->label('Version')
                    ->badge()
                    ->color('gray')
                    ->default('-'),
                IconColumn::make('is_primary')
                    ->label('Primary')
                    ->boolean(),
                TextColumn::make('last_migrated_at')
                    ->label('Last Migration')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('last_backup_at')
                    ->label('Last Backup')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(TenantDatabaseStatus::class),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('markReady')
                    ->label('Set Ready')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (TenantDatabase $record): bool => $record->status !== TenantDatabaseStatus::READY)
                    ->action(function (TenantDatabase $record): void {
                        $record->update(['status' => TenantDatabaseStatus::READY]);
                        Notification::make()->title('Database status updated to Ready')->success()->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
