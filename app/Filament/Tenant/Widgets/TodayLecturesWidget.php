<?php

namespace App\Filament\Tenant\Widgets;

use App\Models\Tenant\ClassSchedule;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TodayLecturesWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ClassSchedule::query()
                    ->with(['classGroup.courseOffering.course', 'room'])
                    ->where('day_of_week', (int) now()->format('N'))
                    ->where('status', 'active')
                    ->orderBy('start_time')
            )
            ->heading('Jadwal Kuliah Hari Ini')
            ->columns([
                TextColumn::make('start_time')
                    ->label('Waktu')
                    ->formatStateUsing(fn (ClassSchedule $record): string => substr((string) $record->start_time, 0, 5).' - '.substr((string) $record->end_time, 0, 5))
                    ->weight('bold'),
                TextColumn::make('classGroup.name')
                    ->label('Kelas')
                    ->description(fn (ClassSchedule $record): ?string => $record->classGroup?->courseOffering?->course?->name)
                    ->searchable(),
                TextColumn::make('room.name')
                    ->label('Ruang')
                    ->placeholder('Belum ditentukan'),
                TextColumn::make('meeting_type')
                    ->label('Pertemuan')
                    ->badge(),
            ])
            ->paginated(false);
    }
}
