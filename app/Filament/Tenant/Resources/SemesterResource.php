<?php

namespace App\Filament\Tenant\Resources;

use App\Enums\Tenant\SemesterType;
use App\Filament\Tenant\Resources\SemesterResource\Pages\CreateSemester;
use App\Filament\Tenant\Resources\SemesterResource\Pages\EditSemester;
use App\Filament\Tenant\Resources\SemesterResource\Pages\ListSemesters;
use App\Models\Tenant\Semester;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SemesterResource extends Resource
{
    protected static ?string $model = Semester::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static string|\UnitEnum|null $navigationGroup = 'Kalender & Periode';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Periode Semester')
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('academic_year_id')
                            ->label('Tahun Akademik')
                            ->relationship('academicYear', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('code')
                            ->label('Kode Semester')
                            ->required()
                            ->maxLength(50),
                        TextInput::make('name')
                            ->label('Nama Semester')
                            ->required()
                            ->maxLength(100),
                        TextInput::make('sequence')
                            ->label('Urutan')
                            ->numeric()
                            ->default(1)
                            ->required(),
                        Select::make('semester_type')
                            ->label('Jenis Semester')
                            ->options([
                                SemesterType::Odd->value => 'Ganjil',
                                SemesterType::Even->value => 'Genap',
                                SemesterType::Short->value => 'Pendek',
                            ])
                            ->default(SemesterType::Odd->value)
                            ->required(),
                        Toggle::make('is_active')
                            ->label('Semester Aktif')
                            ->default(false),
                        DatePicker::make('start_date')
                            ->label('Tanggal Mulai'),
                        DatePicker::make('end_date')
                            ->label('Tanggal Selesai'),
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
                    ->label('Nama Semester')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('academicYear.name')
                    ->label('Tahun Akademik')
                    ->sortable(),
                TextColumn::make('semester_type')
                    ->label('Tipe')
                    ->badge(),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('academic_year_id')
                    ->label('Tahun Akademik')
                    ->relationship('academicYear', 'name'),
                SelectFilter::make('is_active')
                    ->label('Status Aktif')
                    ->options([
                        1 => 'Aktif',
                        0 => 'Tidak Aktif',
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
            'index' => ListSemesters::route('/'),
            'create' => CreateSemester::route('/create'),
            'edit' => EditSemester::route('/{record}/edit'),
        ];
    }
}
