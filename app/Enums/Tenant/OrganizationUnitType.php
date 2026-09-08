<?php

namespace App\Enums\Tenant;

enum OrganizationUnitType: string
{
    case University = 'university';
    case Faculty = 'faculty';
    case StudyProgram = 'study_program';
    case Department = 'department';
    case Bureau = 'bureau';
    case Division = 'division';
    case Center = 'center';
    case Laboratory = 'laboratory';
    case Library = 'library';
    case QualityUnit = 'quality_unit';
    case Other = 'other';
}
