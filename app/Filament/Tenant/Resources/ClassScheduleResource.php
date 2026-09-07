<?php

namespace App\Filament\Tenant\Resources;

use App\Enums\Tenant\MeetingType;
use App\Filament\Tenant\Resources\ClassScheduleResource\Pages\CreateClassSchedule;
use App\Filament\Tenant\Resources\ClassScheduleResource\Pages\EditClassSchedule;
use App\Filament\Tenant\Resources\ClassScheduleResource\Pages\ListClassSchedules;
use App\Models\Tenant\ClassGroup;
use App\Models\Tenant\ClassSchedule;
use App\Models\Tenant\Room;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ClassScheduleResource extends Resource
{
    protected static ?string $model = ClassSchedule::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static string|\UnitEnum|null $navigationGroup = 'Kurikulum & Perkuliahan';

    protected static ?int $navigationSort = 5;

    protected static ?string $modelLabel = 'Jadwal Kuliah';

    protected static ?string $pluralModelLabel = 'Jadwal Kuliah';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Jadwal Perkuliahan')
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('class_group_id')
                            ->label('Kelas & Mata Kuliah')
                            ->relationship('classGroup', 'name')
                            ->getOptionLabelFromRecordUsing(fn (ClassGroup $record): string => "{$record->name} - ".($record->courseOffering?->course?->name ?? 'N/A'))
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('room_id')
                            ->label('Ruang Perkuliahan')
                            ->relationship('room', 'name')
                            ->getOptionLabelFromRecordUsing(fn (Room $record): string => "{$record->name} (".($record->building?->name ?? 'Gedung').')')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('day_of_week')
                            ->label('Hari')
                            ->options([
                                1 => 'Senin',
                                2 => 'Selasa',
                                3 => 'Rabu',
                                4 => 'Kamis',
                                5 => 'Jumat',
                                6 => 'Sabtu',
                                7 => 'Minggu',
                            ])
                            ->required(),
                        Select::make('meeting_type')
                            ->label('Tipe Pertemuan')
                            ->options(MeetingType::class)
                            ->default(MeetingType::Lecture->value)
                            ->required(),
                        TimePicker::make('start_time')
                            ->label('Jam Mulai')
                            ->seconds(false)
                            ->required(),
                        TimePicker::make('end_time')
                            ->label('Jam Selesai')
                            ->seconds(false)
                            ->required(),
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'active' => 'Aktif',
                                'rescheduled' => 'Jadwal Pengganti',
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
                TextColumn::make('day_of_week')
                    ->label('Hari')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn (int $state): string => match ($state) {
                        1 => 'Senin',
                        2 => 'Selasa',
                        3 => 'Rabu',
                        4 => 'Kamis',
                        5 => 'Jumat',
                        6 => 'Sabtu',
                        7 => 'Minggu',
                        default => (string) $state,
                    })
                    ->sortable(),
                TextColumn::make('start_time')
                    ->label('Waktu Kuliah')
                    ->icon('heroicon-o-clock')
                    ->weight('bold')
                    ->formatStateUsing(fn (ClassSchedule $record): string => substr((string) $record->start_time, 0, 5).' - '.substr((string) $record->end_time, 0, 5))
                    ->sortable(),
                TextColumn::make('classGroup.name')
                    ->label('Kelas')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->description(fn (ClassSchedule $record): ?string => $record->classGroup?->courseOffering?->course?->name),
                TextColumn::make('room.name')
                    ->label('Ruang')
                    ->searchable()
                    ->sortable()
                    ->description(fn (ClassSchedule $record): ?string => $record->room?->building?->name),
                TextColumn::make('meeting_type')
                    ->label('Tipe')
                    ->badge()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'active' => 'success',
                        'rescheduled' => 'warning',
                        'cancelled' => 'danger',
                        default => 'secondary',
                    })
                    ->sortable(),
            ])
            ->defaultSort('day_of_week')
            ->filters([
                SelectFilter::make('day_of_week')
                    ->label('Hari')
                    ->options([
                        1 => 'Senin',
                        2 => 'Selasa',
                        3 => 'Rabu',
                        4 => 'Kamis',
                        5 => 'Jumat',
                        6 => 'Sabtu',
                        7 => 'Minggu',
                    ]),
                SelectFilter::make('room_id')
                    ->label('Ruang')
                    ->relationship('room', 'name'),
                SelectFilter::make('meeting_type')
                    ->label('Tipe Pertemuan')
                    ->options(MeetingType::class),
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
            'index' => ListClassSchedules::route('/'),
            'create' => CreateClassSchedule::route('/create'),
            'edit' => EditClassSchedule::route('/{record}/edit'),
        ];
    }
}
