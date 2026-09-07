<?php

namespace App\Filament\Tenant\Resources;

use App\Enums\Tenant\StudyPlanStatus;
use App\Filament\Tenant\Resources\StudyPlanResource\Pages\CreateStudyPlan;
use App\Filament\Tenant\Resources\StudyPlanResource\Pages\EditStudyPlan;
use App\Filament\Tenant\Resources\StudyPlanResource\Pages\ListStudyPlans;
use App\Filament\Tenant\Resources\StudyPlanResource\Pages\ViewStudyPlan;
use App\Models\Tenant\Student;
use App\Models\Tenant\StudyPlan;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StudyPlanResource extends Resource
{
    protected static ?string $model = StudyPlan::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentCheck;

    protected static string|\UnitEnum|null $navigationGroup = 'Kurikulum & Perkuliahan';

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'Kartu Rencana Studi (KRS)';

    protected static ?string $pluralModelLabel = 'Kartu Rencana Studi (KRS)';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Kartu Rencana Studi')
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('student_id')
                            ->label('Mahasiswa')
                            ->relationship('student', 'name')
                            ->getOptionLabelFromRecordUsing(fn (Student $record): string => "{$record->student_number} - {$record->name}")
                            ->searchable(['name', 'student_number'])
                            ->preload()
                            ->required(),
                        Select::make('semester_id')
                            ->label('Semester')
                            ->relationship('semester', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('status')
                            ->label('Status KRS')
                            ->options(StudyPlanStatus::class)
                            ->default(StudyPlanStatus::Draft->value)
                            ->required(),
                        Textarea::make('notes')
                            ->label('Catatan Dosen Wali / Pembimbing')
                            ->columnSpanFull()
                            ->rows(3),
                    ]),
                ]),

            Section::make('Mata Kuliah Yang Diambil')
                ->schema([
                    Repeater::make('items')
                        ->relationship('items')
                        ->schema([
                            Select::make('course_id')
                                ->label('Mata Kuliah')
                                ->relationship('course', 'name')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->columnSpan(2),
                            Select::make('class_group_id')
                                ->label('Kelas')
                                ->relationship('classGroup', 'name')
                                ->searchable()
                                ->preload()
                                ->nullable()
                                ->columnSpan(1),
                            TextInput::make('credit_units')
                                ->label('SKS')
                                ->numeric()
                                ->default(3)
                                ->required()
                                ->columnSpan(1),
                        ])
                        ->columns(4)
                        ->defaultItems(1)
                        ->addActionLabel('+ Tambah Mata Kuliah'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.student_number')
                    ->label('NIM')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->badge()
                    ->color('gray'),
                TextColumn::make('student.name')
                    ->label('Mahasiswa')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (StudyPlan $record): ?string => $record->student?->studyProgram?->name),
                TextColumn::make('semester.name')
                    ->label('Semester')
                    ->sortable(),
                TextColumn::make('items_count')
                    ->counts('items')
                    ->label('Total MK')
                    ->badge()
                    ->color('info')
                    ->suffix(' MK'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('submitted_at')
                    ->label('Diajukan')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->placeholder('-'),
                TextColumn::make('approved_at')
                    ->label('Disetujui')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('semester_id')
                    ->label('Semester')
                    ->relationship('semester', 'name'),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(StudyPlanStatus::class),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (StudyPlan $record): bool => $record->status !== StudyPlanStatus::Approved)
                    ->action(function (StudyPlan $record): void {
                        $record->update([
                            'status' => StudyPlanStatus::Approved,
                            'approved_at' => now(),
                        ]);
                        Notification::make()->title('KRS Mahasiswa berhasil disetujui')->success()->send();
                    }),
                Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (StudyPlan $record): bool => $record->status === StudyPlanStatus::Submitted)
                    ->action(function (StudyPlan $record): void {
                        $record->update([
                            'status' => StudyPlanStatus::Rejected,
                        ]);
                        Notification::make()->title('KRS Mahasiswa ditolak')->danger()->send();
                    }),
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
            'index' => ListStudyPlans::route('/'),
            'create' => CreateStudyPlan::route('/create'),
            'view' => ViewStudyPlan::route('/{record}'),
            'edit' => EditStudyPlan::route('/{record}/edit'),
        ];
    }
}
