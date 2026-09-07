<?php

namespace App\Filament\Resources\TenantSubscriptions\Tables;

use App\Enums\Landlord\SubscriptionStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TenantSubscriptionsTable
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
                TextColumn::make('package.name')
                    ->label('Package')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (SubscriptionStatus|string|null $state): string => match ($state instanceof SubscriptionStatus ? $state : SubscriptionStatus::tryFrom((string) $state)) {
                        SubscriptionStatus::ACTIVE => 'success',
                        SubscriptionStatus::TRIAL, SubscriptionStatus::TRIALING => 'info',
                        SubscriptionStatus::PAST_DUE => 'warning',
                        SubscriptionStatus::EXPIRED, SubscriptionStatus::CANCELLED => 'danger',
                        default => 'secondary',
                    })
                    ->sortable(),
                TextColumn::make('price')
                    ->label('Price')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('starts_at')
                    ->label('Starts')
                    ->dateTime('d M Y')
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->label('Expires')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->placeholder('Never / Lifetime'),
                IconColumn::make('auto_renew')
                    ->label('Auto-Renew')
                    ->boolean(),
            ])
            ->defaultSort('starts_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(SubscriptionStatus::class),
                SelectFilter::make('package')
                    ->relationship('package', 'name'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
