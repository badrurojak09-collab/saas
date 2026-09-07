<?php

namespace App\Enums\Tenant;

enum AssessmentType: string
{
    case Assignment = 'assignment';
    case Quiz = 'quiz';
    case MidtermExam = 'midterm_exam';
    case FinalExam = 'final_exam';
    case Project = 'project';
    case Practicum = 'practicum';
}
