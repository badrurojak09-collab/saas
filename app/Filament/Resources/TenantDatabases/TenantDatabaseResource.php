<?php

namespace App\Filament\Resources\TenantDatabases;

use App\Filament\Resources\TenantDatabases\Pages\CreateTenantDatabase;
use App\Filament\Resources\TenantDatabases\Pages\EditTenantDatabase;
use App\Filament\Resources\TenantDatabases\Pages\ListTenantDatabases;
use App\Filament\Resources\TenantDatabases\RelationManagers\MigrationVersionsRelationManager;
use App\Filament\Resources\TenantDatabases\RelationManagers\OperationsRelationManager;
use App\Filament\Resources\TenantDatabases\Schemas\TenantDatabaseForm;
use App\Filament\Resources\TenantDatabases\Tables\TenantDatabasesTable;
use App\Models\Landlord\TenantDatabase;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TenantDatabaseResource extends Resource
{
    protected static ?string $model = TenantDatabase::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCircleStack;

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'database';

    public static function form(Schema $schema): Schema
    {
        return TenantDatabaseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TenantDatabasesTable::configure($table);
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Tenancy & Infrastruktur';
    }

    public static function getRelations(): array
    {
        return [
            OperationsRelationManager::class,
            MigrationVersionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTenantDatabases::route('/'),
            'create' => CreateTenantDatabase::route('/create'),
            'edit' => EditTenantDatabase::route('/{record}/edit'),
        ];
    }
}
