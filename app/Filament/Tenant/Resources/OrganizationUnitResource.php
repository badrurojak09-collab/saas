<?php

namespace App\Filament\Tenant\Resources;

use App\Enums\Tenant\OrganizationUnitStatus;
use App\Enums\Tenant\OrganizationUnitType;
use App\Filament\Tenant\Resources\OrganizationUnitResource\Pages\CreateOrganizationUnit;
use App\Filament\Tenant\Resources\OrganizationUnitResource\Pages\EditOrganizationUnit;
use App\Filament\Tenant\Resources\OrganizationUnitResource\Pages\ListOrganizationUnits;
use App\Models\Tenant\OrganizationUnit;
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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class OrganizationUnitResource extends Resource
{
    protected static ?string $model = OrganizationUnit::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingLibrary;

    protected static string|\UnitEnum|null $navigationGroup = 'Organisasi';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Unit Organisasi')
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('perguruan_tinggi_id')
                            ->label('Perguruan Tinggi')
                            ->relationship('perguruanTinggi', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('parent_id')
                            ->label('Unit Induk')
                            ->relationship('parent', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        TextInput::make('code')
                            ->label('Kode Unit')
                            ->required()
                            ->maxLength(50),
                        TextInput::make('name')
                            ->label('Nama Unit')
                            ->required()
                            ->maxLength(150),
                        TextInput::make('short_name')
                            ->label('Singkatan')
                            ->maxLength(75),
                        Select::make('type')
                            ->label('Tipe')
                            ->options(OrganizationUnitType::class)
                            ->required(),
                        Select::make('status')
                            ->label('Status')
                            ->options(OrganizationUnitStatus::class)
                            ->default(OrganizationUnitStatus::Active->value)
                            ->required(),
                        TextInput::make('description')
                            ->label('Deskripsi')
                            ->maxLength(500)
                            ->nullable(),
                        TextInput::make('sort_order')
                            ->label('Urutan')
                            ->numeric()
                            ->default(0),
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
                    ->label('Nama Unit')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge(),

                TextColumn::make('parent.name')
                    ->label('Unit Induk')
                    ->sortable(),

                TextColumn::make('perguruanTinggi.name')
                    ->label('Perguruan Tinggi')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),

                TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Tipe')
                    ->options(OrganizationUnitType::class),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options(OrganizationUnitStatus::class),

                SelectFilter::make('perguruan_tinggi_id')
                    ->label('Perguruan Tinggi')
                    ->relationship('perguruanTinggi', 'name'),
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
            'index' => ListOrganizationUnits::route('/'),
            'create' => CreateOrganizationUnit::route('/create'),
            'edit' => EditOrganizationUnit::route('/{record}/edit'),
        ];
    }
}
