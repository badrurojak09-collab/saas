<?php

namespace App\Filament\Tenant\Resources;

use App\Enums\Tenant\ClassType;
use App\Filament\Tenant\Resources\CourseOfferingResource\Pages\CreateCourseOffering;
use App\Filament\Tenant\Resources\CourseOfferingResource\Pages\EditCourseOffering;
use App\Filament\Tenant\Resources\CourseOfferingResource\Pages\ListCourseOfferings;
use App\Models\Tenant\CourseOffering;
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

class CourseOfferingResource extends Resource
{
    protected static ?string $model = CourseOffering::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string|\UnitEnum|null $navigationGroup = 'Kurikulum & Perkuliahan';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Penawaran Kelas Kuliah')
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('semester_id')
                            ->label('Semester')
                            ->relationship('semester', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('study_program_id')
                            ->label('Program Studi')
                            ->relationship('studyProgram', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('curriculum_id')
                            ->label('Kurikulum')
                            ->relationship('curriculum', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('course_id')
                            ->label('Mata Kuliah')
                            ->relationship('course', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('code')
                            ->label('Kode Kelas (misal: IF-101-A)')
                            ->required()
                            ->maxLength(50),
                        Select::make('class_type')
                            ->label('Jenis Kelas')
                            ->options(ClassType::class)
                            ->default(ClassType::Regular->value)
                            ->required(),
                        TextInput::make('capacity')
                            ->label('Kapasitas Peserta')
                            ->numeric()
                            ->default(40)
                            ->required(),
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'active' => 'Aktif',
                                'inactive' => 'Nonaktif',
                                'cancelled' => 'Dibatalkan',
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
                    ->label('Kode Kelas')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->weight('bold'),
                TextColumn::make('course.name')
                    ->label('Mata Kuliah')
                    ->searchable()
                    ->sortable()
                    ->description(fn (CourseOffering $record): ?string => $record->course?->code),
                TextColumn::make('semester.name')
                    ->label('Semester')
                    ->sortable(),
                TextColumn::make('studyProgram.name')
                    ->label('Program Studi')
                    ->sortable(),
                TextColumn::make('class_type')
                    ->label('Tipe')
                    ->badge(),
                TextColumn::make('capacity')
                    ->label('Kapasitas')
                    ->numeric()
                    ->sortable()
                    ->suffix(' Mhs'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'gray',
                        'cancelled' => 'danger',
                        default => 'secondary',
                    }),
            ])
            ->filters([
                SelectFilter::make('semester_id')
                    ->label('Semester')
                    ->relationship('semester', 'name'),
                SelectFilter::make('study_program_id')
                    ->label('Program Studi')
                    ->relationship('studyProgram', 'name'),
                SelectFilter::make('class_type')
                    ->label('Jenis Kelas')
                    ->options(ClassType::class),
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
            'index' => ListCourseOfferings::route('/'),
            'create' => CreateCourseOffering::route('/create'),
            'edit' => EditCourseOffering::route('/{record}/edit'),
        ];
    }
}
