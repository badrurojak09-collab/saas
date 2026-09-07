<?php

namespace App\Filament\Resources\Packages\Tables;

use App\Enums\Landlord\BillingCycle;
use App\Enums\Landlord\PackageStatus;
use App\Models\Landlord\Package;
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

class PackagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Package')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (Package $record): ?string => $record->code),
                TextColumn::make('price')
                    ->label('Price')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('billing_cycle')
                    ->label('Billing Cycle')
                    ->badge()
                    ->color('primary'),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('features_count')
                    ->counts('features')
                    ->label('Features'),
                TextColumn::make('subscriptions_count')
                    ->counts('subscriptions')
                    ->label('Active Subs')
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->filters([
                SelectFilter::make('billing_cycle')
                    ->options(BillingCycle::class),
                SelectFilter::make('status')
                    ->options(PackageStatus::class),
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
