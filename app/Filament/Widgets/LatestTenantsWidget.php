<?php

namespace App\Filament\Widgets;

use App\Enums\Landlord\TenantDatabaseStatus;
use App\Models\Landlord\Tenant;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestTenantsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Tenant::query()->with(['primaryDomain', 'database'])->latest()
            )
            ->heading('Daftar Tenant Kampus Terbaru')
            ->columns([
                TextColumn::make('name')
                    ->label('Institusi / Kampus')
                    ->weight('bold')
                    ->description(fn (Tenant $record): ?string => $record->legal_name),
                TextColumn::make('code')
                    ->label('Kode')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('primaryDomain.domain')
                    ->label('Domain Utama')
                    ->icon(Heroicon::OutlinedGlobeAlt)
                    ->color('primary')
                    ->default('-'),
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
                    ->formatStateUsing(fn ($state): string => $state instanceof TenantDatabaseStatus ? ($state->getLabel() ?? ucfirst($state->value)) : ($state ? ucfirst((string) $state) : 'Unassigned')),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
                TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->dateTime('d M Y, H:i'),
            ])
            ->recordActions([
                Action::make('openPortal')
                    ->label('Buka Tenant')
                    ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                    ->color('info')
                    ->url(fn (Tenant $record): string => url('/tenant?tenant='.($record->slug ?: $record->code)))
                    ->openUrlInNewTab(),
            ])
            ->paginated([5, 10]);
    }
}
