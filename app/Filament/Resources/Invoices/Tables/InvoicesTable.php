<?php

namespace App\Filament\Resources\Invoices\Tables;

use App\Enums\Landlord\InvoiceStatus;
use App\Models\Landlord\Invoice;
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

class InvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('Invoice No.')
                    ->searchable()
                    ->copyable()
                    ->weight('bold')
                    ->icon('heroicon-o-document-text'),
                TextColumn::make('tenant.name')
                    ->label('Tenant')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('total')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('currency')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('issued_at')
                    ->label('Issued')
                    ->dateTime('d M Y')
                    ->sortable(),
                TextColumn::make('due_at')
                    ->label('Due Date')
                    ->dateTime('d M Y')
                    ->sortable(),
                TextColumn::make('paid_at')
                    ->label('Paid At')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->placeholder('Unpaid')
                    ->toggleable(),
            ])
            ->defaultSort('issued_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(InvoiceStatus::class),
                SelectFilter::make('tenant')
                    ->relationship('tenant', 'name'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('markPaid')
                    ->label('Mark Paid')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Invoice $record): bool => $record->status !== InvoiceStatus::PAID)
                    ->action(function (Invoice $record): void {
                        $record->update([
                            'status' => InvoiceStatus::PAID,
                            'paid_at' => now(),
                        ]);
                        Notification::make()->title('Invoice marked as paid')->success()->send();
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
