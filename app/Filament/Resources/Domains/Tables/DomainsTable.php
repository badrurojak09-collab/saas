<?php

namespace App\Filament\Resources\Domains\Tables;

use App\Enums\Landlord\DomainType;
use App\Models\Landlord\Domain;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class DomainsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('domain')
                    ->label('Domain')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-o-globe-alt')
                    ->copyable(),
                TextColumn::make('tenant.name')
                    ->label('Tenant')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->badge()
                    ->color(fn (DomainType|string|null $state): string => match ($state instanceof DomainType ? $state : DomainType::tryFrom((string) $state)) {
                        DomainType::CUSTOM => 'primary',
                        default => 'gray',
                    }),
                IconColumn::make('is_primary')
                    ->label('Primary')
                    ->boolean(),
                IconColumn::make('is_verified')
                    ->label('Verified')
                    ->boolean(),
                TextColumn::make('ssl_status')
                    ->label('SSL')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'failed' => 'danger',
                        default => 'warning',
                    }),
                TextColumn::make('verified_at')
                    ->label('Verified At')
                    ->dateTime('d M Y')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options(DomainType::class),
                SelectFilter::make('ssl_status')
                    ->options([
                        'active' => 'Active',
                        'pending' => 'Pending',
                        'failed' => 'Failed',
                    ]),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('verify')
                    ->label('Verify')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn (Domain $record): bool => ! $record->is_verified)
                    ->action(function (Domain $record): void {
                        $record->update([
                            'is_verified' => true,
                            'verified_at' => now(),
                            'ssl_status' => 'active',
                        ]);
                        Notification::make()->title('Domain verified successfully')->success()->send();
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
