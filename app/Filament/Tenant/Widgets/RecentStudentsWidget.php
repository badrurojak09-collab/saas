<?php

namespace App\Filament\Tenant\Widgets;

use App\Models\Tenant\Student;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentStudentsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Student::query()->with('studyProgram')->latest()->limit(5)
            )
            ->heading('Mahasiswa Terbaru Terdaftar')
            ->columns([
                TextColumn::make('student_number')
                    ->label('NIM')
                    ->copyable()
                    ->badge()
                    ->color('gray'),
                TextColumn::make('name')
                    ->label('Nama Mahasiswa')
                    ->weight('bold'),
                TextColumn::make('studyProgram.name')
                    ->label('Program Studi')
                    ->badge()
                    ->color('info'),
                TextColumn::make('entry_year')
                    ->label('Angkatan')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
                TextColumn::make('created_at')
                    ->label('Waktu Registrasi')
                    ->dateTime('d M Y, H:i'),
            ])
            ->paginated(false);
    }
}
