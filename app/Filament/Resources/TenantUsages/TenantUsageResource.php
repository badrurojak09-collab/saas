<?php

namespace App\Filament\Resources\TenantUsages;

use App\Filament\Resources\TenantUsages\Pages\CreateTenantUsage;
use App\Filament\Resources\TenantUsages\Pages\EditTenantUsage;
use App\Filament\Resources\TenantUsages\Pages\ListTenantUsages;
use App\Filament\Resources\TenantUsages\Schemas\TenantUsageForm;
use App\Filament\Resources\TenantUsages\Tables\TenantUsagesTable;
use App\Models\Landlord\TenantUsage;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TenantUsageResource extends Resource
{
    protected static ?string $model = TenantUsage::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBarSquare;

    public static function form(Schema $schema): Schema
    {
        return TenantUsageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TenantUsagesTable::configure($table);
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Platform & Keamanan';
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTenantUsages::route('/'),
            'create' => CreateTenantUsage::route('/create'),
            'edit' => EditTenantUsage::route('/{record}/edit'),
        ];
    }
}
