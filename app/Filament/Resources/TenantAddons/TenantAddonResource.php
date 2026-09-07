<?php

namespace App\Filament\Resources\TenantAddons;

use App\Filament\Resources\TenantAddons\Pages\CreateTenantAddon;
use App\Filament\Resources\TenantAddons\Pages\EditTenantAddon;
use App\Filament\Resources\TenantAddons\Pages\ListTenantAddons;
use App\Filament\Resources\TenantAddons\Schemas\TenantAddonForm;
use App\Filament\Resources\TenantAddons\Tables\TenantAddonsTable;
use App\Models\Landlord\TenantAddon;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TenantAddonResource extends Resource
{
    protected static ?string $model = TenantAddon::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedSquaresPlus;

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'Tenant Addon';

    public static function form(Schema $schema): Schema
    {
        return TenantAddonForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TenantAddonsTable::configure($table);
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Langganan & Penagihan';
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
            'index' => ListTenantAddons::route('/'),
            'create' => CreateTenantAddon::route('/create'),
            'edit' => EditTenantAddon::route('/{record}/edit'),
        ];
    }
}
