<?php

namespace App\Filament\Tenant\Resources;

use App\Enums\Tenant\DegreeLevel;
use App\Filament\Tenant\Resources\StudyProgramResource\Pages\CreateStudyProgram;
use App\Filament\Tenant\Resources\StudyProgramResource\Pages\EditStudyProgram;
use App\Filament\Tenant\Resources\StudyProgramResource\Pages\ListStudyPrograms;
use App\Models\Tenant\StudyProgram;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
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

class StudyProgramResource extends Resource
{
    protected static ?string $model = StudyProgram::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static string|\UnitEnum|null $navigationGroup = 'Organisasi';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Program Studi')
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('department_id')
                            ->label('Departemen')
                            ->relationship('department', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('code')
                            ->label('Kode Prodi')
                            ->required()
                            ->maxLength(50),
                        TextInput::make('name')
                            ->label('Nama Program Studi')
                            ->required()
                            ->maxLength(150),
                        Select::make('degree_level')
                            ->label('Jenjang')
                            ->options(DegreeLevel::class)
                            ->default(DegreeLevel::S1->value)
                            ->required(),
                        TextInput::make('accreditation_status')
                            ->label('Status Akreditasi')
                            ->maxLength(50),
                        TextInput::make('accreditation_number')
                            ->label('Nomor SK Akreditasi')
                            ->maxLength(100),
                        DatePicker::make('accreditation_expired_at')
                            ->label('Kadaluarsa Akreditasi'),
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'active' => 'Aktif',
                                'inactive' => 'Nonaktif',
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
                    ->label('Nama Program Studi')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('degree_level')
                    ->label('Jenjang')
                    ->badge(),
                TextColumn::make('department.name')
                    ->label('Departemen')
                    ->sortable(),
                TextColumn::make('accreditation_status')
                    ->label('Akreditasi')
                    ->badge(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
            ])
            ->filters([
                SelectFilter::make('department_id')
                    ->label('Departemen')
                    ->relationship('department', 'name'),
                SelectFilter::make('degree_level')
                    ->label('Jenjang')
                    ->options(DegreeLevel::class),
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
            'index' => ListStudyPrograms::route('/'),
            'create' => CreateStudyProgram::route('/create'),
            'edit' => EditStudyProgram::route('/{record}/edit'),
        ];
    }
}
