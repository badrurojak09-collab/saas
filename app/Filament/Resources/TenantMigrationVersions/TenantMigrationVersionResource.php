<?php

namespace App\Filament\Resources\TenantMigrationVersions;

use App\Filament\Resources\TenantMigrationVersions\Pages\CreateTenantMigrationVersion;
use App\Filament\Resources\TenantMigrationVersions\Pages\EditTenantMigrationVersion;
use App\Filament\Resources\TenantMigrationVersions\Pages\ListTenantMigrationVersions;
use App\Filament\Resources\TenantMigrationVersions\Schemas\TenantMigrationVersionForm;
use App\Filament\Resources\TenantMigrationVersions\Tables\TenantMigrationVersionsTable;
use App\Models\Landlord\TenantMigrationVersion;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TenantMigrationVersionResource extends Resource
{
    protected static ?string $model = TenantMigrationVersion::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowPath;

    public static function form(Schema $schema): Schema
    {
        return TenantMigrationVersionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TenantMigrationVersionsTable::configure($table);
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Penyediaan & Operasional';
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
            'index' => ListTenantMigrationVersions::route('/'),
            'create' => CreateTenantMigrationVersion::route('/create'),
            'edit' => EditTenantMigrationVersion::route('/{record}/edit'),
        ];
    }
}
