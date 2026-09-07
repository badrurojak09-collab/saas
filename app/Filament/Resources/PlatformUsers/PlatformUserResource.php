<?php

namespace App\Filament\Resources\PlatformUsers;

use App\Filament\Resources\PlatformUsers\Pages\CreatePlatformUser;
use App\Filament\Resources\PlatformUsers\Pages\EditPlatformUser;
use App\Filament\Resources\PlatformUsers\Pages\ListPlatformUsers;
use App\Filament\Resources\PlatformUsers\Schemas\PlatformUserForm;
use App\Filament\Resources\PlatformUsers\Tables\PlatformUsersTable;
use App\Models\Landlord\PlatformUser;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PlatformUserResource extends Resource
{
    protected static ?string $model = PlatformUser::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    public static function form(Schema $schema): Schema
    {
        return PlatformUserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PlatformUsersTable::configure($table);
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Platform & Keamanan';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
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
            'index' => ListPlatformUsers::route('/'),
            'create' => CreatePlatformUser::route('/create'),
            'edit' => EditPlatformUser::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
