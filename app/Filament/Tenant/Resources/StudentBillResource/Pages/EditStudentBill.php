<?php

namespace App\Filament\Tenant\Resources\StudentBillResource\Pages;

use App\Filament\Tenant\Resources\StudentBillResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditStudentBill extends EditRecord
{
    protected static string $resource = StudentBillResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
