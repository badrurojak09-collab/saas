<?php

namespace App\Filament\Tenant\Widgets;

use App\Enums\Tenant\StudentStatus;
use App\Models\Tenant\Lecturer;
use App\Models\Tenant\Semester;
use App\Models\Tenant\Student;
use App\Models\Tenant\StudyProgram;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TenantOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalStudents = Student::count();
        $activeStudents = Student::where('status', StudentStatus::Active)->count();

        $totalLecturers = Lecturer::count();
        $totalStudyPrograms = StudyProgram::count();

        /** @var Semester|null $activeSemester */
        $activeSemester = Semester::with('academicYear')->where('is_active', true)->first();
        $semesterLabel = $activeSemester ? $activeSemester->name : 'Belum aktif';
        $ayLabel = $activeSemester?->academicYear?->name ?? 'Belum ditentukan';

        return [
            Stat::make('Mahasiswa', (string) $totalStudents)
                ->description("{$activeStudents} mahasiswa aktif")
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),

            Stat::make('Dosen & Pengajar', (string) $totalLecturers)
                ->description('Total tenaga pengajar')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('success'),

            Stat::make('Program Studi', (string) $totalStudyPrograms)
                ->description('Prodi terdaftar')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('info'),

            Stat::make('Semester Berjalan', $semesterLabel)
                ->description($ayLabel)
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('warning'),
        ];
    }
}
