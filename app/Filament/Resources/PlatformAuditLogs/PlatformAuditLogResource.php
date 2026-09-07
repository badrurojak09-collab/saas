<?php

namespace App\Filament\Resources\PlatformAuditLogs;

use App\Filament\Resources\PlatformAuditLogs\Pages\CreatePlatformAuditLog;
use App\Filament\Resources\PlatformAuditLogs\Pages\EditPlatformAuditLog;
use App\Filament\Resources\PlatformAuditLogs\Pages\ListPlatformAuditLogs;
use App\Filament\Resources\PlatformAuditLogs\Schemas\PlatformAuditLogForm;
use App\Filament\Resources\PlatformAuditLogs\Tables\PlatformAuditLogsTable;
use App\Models\Landlord\PlatformAuditLog;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PlatformAuditLogResource extends Resource
{
    protected static ?string $model = PlatformAuditLog::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    public static function form(Schema $schema): Schema
    {
        return PlatformAuditLogForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PlatformAuditLogsTable::configure($table);
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Platform & Keamanan';
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
            'index' => ListPlatformAuditLogs::route('/'),
            'create' => CreatePlatformAuditLog::route('/create'),
            'edit' => EditPlatformAuditLog::route('/{record}/edit'),
        ];
    }
}
