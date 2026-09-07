<?php

namespace App\Enums\Tenant;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum MeetingType: string implements HasColor, HasLabel
{
    case Lecture = 'lecture';
    case Lab = 'lab';
    case Seminar = 'seminar';
    case Exam = 'exam';
    case FieldStudy = 'field_study';

    public function getLabel(): string
    {
        return match ($this) {
            self::Lecture => 'Kuliah Teori',
            self::Lab => 'Praktikum',
            self::Seminar => 'Seminar',
            self::Exam => 'Ujian',
            self::FieldStudy => 'Kuliah Lapangan',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Lecture => 'primary',
            self::Lab => 'info',
            self::Seminar => 'purple',
            self::Exam => 'danger',
            self::FieldStudy => 'success',
        };
    }
}
