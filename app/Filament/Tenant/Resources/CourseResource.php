<?php

namespace App\Filament\Tenant\Resources;

use App\Enums\Tenant\CourseCategory;
use App\Enums\Tenant\CourseType;
use App\Enums\Tenant\GradingType;
use App\Filament\Tenant\Resources\CourseResource\Pages\CreateCourse;
use App\Filament\Tenant\Resources\CourseResource\Pages\EditCourse;
use App\Filament\Tenant\Resources\CourseResource\Pages\ListCourses;
use App\Models\Tenant\Course;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static string|\UnitEnum|null $navigationGroup = 'Kurikulum & Perkuliahan';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Mata Kuliah')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('code')
                            ->label('Kode MK')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),
                        TextInput::make('name')
                            ->label('Nama Mata Kuliah')
                            ->required()
                            ->maxLength(150),
                        TextInput::make('short_name')
                            ->label('Singkatan')
                            ->maxLength(50),
                        TextInput::make('credit_units')
                            ->label('Bobot SKS')
                            ->numeric()
                            ->default(3.0)
                            ->required(),
                        Select::make('course_type')
                            ->label('Tipe MK')
                            ->options(array_combine(
                                array_map(fn ($case) => $case->value, CourseType::cases()),
                                array_map(fn ($case) => ucfirst($case->value), CourseType::cases())
                            ))
                            ->default(CourseType::Mandatory->value)
                            ->required(),
                        Select::make('course_category')
                            ->label('Kategori MK')
                            ->options(array_combine(
                                array_map(fn ($case) => $case->value, CourseCategory::cases()),
                                array_map(fn ($case) => ucfirst($case->value), CourseCategory::cases())
                            ))
                            ->default(CourseCategory::Major->value)
                            ->required(),
                        Select::make('grading_type')
                            ->label('Sistem Penilaian')
                            ->options(array_combine(
                                array_map(fn ($case) => $case->value, GradingType::cases()),
                                array_map(fn ($case) => ucfirst($case->value), GradingType::cases())
                            ))
                            ->default(GradingType::StandardLetter->value)
                            ->required(),
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'active' => 'Aktif',
                                'inactive' => 'Nonaktif',
                            ])
                            ->default('active')
                            ->required(),
                    ]),
                    Textarea::make('description')
                        ->label('Deskripsi / Silabus Singkat')
                        ->rows(3),
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
                    ->label('Nama Mata Kuliah')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('credit_units')
                    ->label('SKS')
                    ->sortable()
                    ->badge(),
                TextColumn::make('course_type')
                    ->label('Tipe')
                    ->badge(),
                TextColumn::make('course_category')
                    ->label('Kategori')
                    ->badge(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
            ])
            ->filters([
                SelectFilter::make('course_type')
                    ->label('Tipe MK')
                    ->options(array_combine(
                        array_map(fn ($case) => $case->value, CourseType::cases()),
                        array_map(fn ($case) => ucfirst($case->value), CourseType::cases())
                    )),
                SelectFilter::make('course_category')
                    ->label('Kategori MK')
                    ->options(array_combine(
                        array_map(fn ($case) => $case->value, CourseCategory::cases()),
                        array_map(fn ($case) => ucfirst($case->value), CourseCategory::cases())
                    )),
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Aktif',
                        'inactive' => 'Nonaktif',
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
            'index' => ListCourses::route('/'),
            'create' => CreateCourse::route('/create'),
            'edit' => EditCourse::route('/{record}/edit'),
        ];
    }
}
