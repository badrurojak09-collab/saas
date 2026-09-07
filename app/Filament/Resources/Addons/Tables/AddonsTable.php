<?php

namespace App\Filament\Resources\Addons\Tables;

use App\Enums\Landlord\AddonStatus;
use App\Enums\Landlord\BillingCycle;
use App\Models\Landlord\Addon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class AddonsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Addon')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (Addon $record): ?string => $record->code),
                TextColumn::make('price')
                    ->label('Price')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('billing_cycle')
                    ->label('Billing Cycle')
                    ->badge()
                    ->color('primary'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (AddonStatus|string|null $state): string => match ($state instanceof AddonStatus ? $state : AddonStatus::tryFrom((string) $state)) {
                        AddonStatus::ACTIVE => 'success',
                        AddonStatus::INACTIVE => 'gray',
                        default => 'secondary',
                    }),
                TextColumn::make('features_count')
                    ->counts('features')
                    ->label('Features'),
                TextColumn::make('tenant_addons_count')
                    ->counts('tenantAddons')
                    ->label('Active Installs')
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('billing_cycle')
                    ->options(BillingCycle::class),
                SelectFilter::make('status')
                    ->options(AddonStatus::class),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
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
