<?php

namespace App\Filament\Tenant\Resources;

use App\Enums\Tenant\RoomType;
use App\Filament\Tenant\Resources\RoomResource\Pages\CreateRoom;
use App\Filament\Tenant\Resources\RoomResource\Pages\EditRoom;
use App\Filament\Tenant\Resources\RoomResource\Pages\ListRooms;
use App\Models\Tenant\Room;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RoomResource extends Resource
{
    protected static ?string $model = Room::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static string|\UnitEnum|null $navigationGroup = 'Fasilitas & Ruang';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Ruang')
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('building_id')
                            ->label('Gedung')
                            ->relationship('building', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('code')
                            ->label('Kode Ruang')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),
                        TextInput::make('name')
                            ->label('Nama Ruang')
                            ->required()
                            ->maxLength(100),
                        Select::make('room_type')
                            ->label('Jenis Ruang')
                            ->options(RoomType::class)
                            ->default(RoomType::Classroom->value)
                            ->required(),
                        TextInput::make('capacity')
                            ->label('Kapasitas (Orang)')
                            ->numeric()
                            ->default(40)
                            ->required(),
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'active' => 'Aktif',
                                'inactive' => 'Nonaktif',
                                'maintenance' => 'Perbaikan',
                            ])
                            ->default('active')
                            ->required(),
                    ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable()
                    ->badge(),
                TextColumn::make('name')
                    ->label('Nama Ruang')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (Room $record): ?string => $record->building?->name),
                TextColumn::make('room_type')
                    ->label('Tipe')
                    ->badge(),
                TextColumn::make('capacity')
                    ->label('Kapasitas')
                    ->numeric()
                    ->sortable()
                    ->suffix(' Kursi'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'gray',
                        'maintenance' => 'warning',
                        default => 'secondary',
                    }),
            ])
            ->filters([
                SelectFilter::make('building_id')
                    ->label('Gedung')
                    ->relationship('building', 'name'),
                SelectFilter::make('room_type')
                    ->label('Tipe Ruang')
                    ->options(RoomType::class),
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Aktif',
                        'inactive' => 'Nonaktif',
                        'maintenance' => 'Perbaikan',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRooms::route('/'),
            'create' => CreateRoom::route('/create'),
            'edit' => EditRoom::route('/{record}/edit'),
        ];
    }
}
