<?php

namespace App\Filament\Tenant\Resources;

use App\Filament\Tenant\Resources\CurriculumResource\Pages\CreateCurriculum;
use App\Filament\Tenant\Resources\CurriculumResource\Pages\EditCurriculum;
use App\Filament\Tenant\Resources\CurriculumResource\Pages\ListCurriculums;
use App\Models\Tenant\Curriculum;
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

class CurriculumResource extends Resource
{
    protected static ?string $model = Curriculum::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedQueueList;

    protected static string|\UnitEnum|null $navigationGroup = 'Kurikulum & Perkuliahan';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Kurikulum')
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('study_program_id')
                            ->label('Program Studi')
                            ->relationship('studyProgram', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('code')
                            ->label('Kode Kurikulum')
                            ->required()
                            ->maxLength(50),
                        TextInput::make('name')
                            ->label('Nama Kurikulum')
                            ->required()
                            ->maxLength(150),
                        TextInput::make('effective_start_year')
                            ->label('Tahun Mulai Berlaku')
                            ->numeric()
                            ->default(date('Y'))
                            ->required(),
                        TextInput::make('effective_end_year')
                            ->label('Tahun Berakhir')
                            ->numeric(),
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'active' => 'Aktif',
                                'inactive' => 'Nonaktif',
                                'draft' => 'Draf',
                            ])
                            ->default('active')
                            ->required(),
                    ]),
                    Textarea::make('description')
                        ->label('Deskripsi')
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
                    ->label('Nama Kurikulum')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('studyProgram.name')
                    ->label('Program Studi')
                    ->sortable(),
                TextColumn::make('effective_start_year')
                    ->label('Tahun Berlaku')
                    ->sortable(),
                TextColumn::make('curriculum_courses_count')
                    ->label('Mata Kuliah')
                    ->counts('curriculumCourses'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
            ])
            ->filters([
                SelectFilter::make('study_program_id')
                    ->label('Program Studi')
                    ->relationship('studyProgram', 'name'),
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Aktif',
                        'inactive' => 'Nonaktif',
                        'draft' => 'Draf',
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
            'index' => ListCurriculums::route('/'),
            'create' => CreateCurriculum::route('/create'),
            'edit' => EditCurriculum::route('/{record}/edit'),
        ];
    }
}
