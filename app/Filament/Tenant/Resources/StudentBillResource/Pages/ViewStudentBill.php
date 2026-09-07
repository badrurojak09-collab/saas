<?php

namespace App\Filament\Tenant\Resources\StudentBillResource\Pages;

use App\Filament\Tenant\Resources\StudentBillResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewStudentBill extends ViewRecord
{
    protected static string $resource = StudentBillResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
