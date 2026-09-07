<?php

namespace App\Filament\Resources\TenantDatabaseOperations;

use App\Filament\Resources\TenantDatabaseOperations\Pages\CreateTenantDatabaseOperation;
use App\Filament\Resources\TenantDatabaseOperations\Pages\EditTenantDatabaseOperation;
use App\Filament\Resources\TenantDatabaseOperations\Pages\ListTenantDatabaseOperations;
use App\Filament\Resources\TenantDatabaseOperations\Schemas\TenantDatabaseOperationForm;
use App\Filament\Resources\TenantDatabaseOperations\Tables\TenantDatabaseOperationsTable;
use App\Models\Landlord\TenantDatabaseOperation;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TenantDatabaseOperationResource extends Resource
{
    protected static ?string $model = TenantDatabaseOperation::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCodeBracket;

    public static function form(Schema $schema): Schema
    {
        return TenantDatabaseOperationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TenantDatabaseOperationsTable::configure($table);
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Penyediaan & Operasional';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
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
            'index' => ListTenantDatabaseOperations::route('/'),
            'create' => CreateTenantDatabaseOperation::route('/create'),
            'edit' => EditTenantDatabaseOperation::route('/{record}/edit'),
        ];
    }
}
