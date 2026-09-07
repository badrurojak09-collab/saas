<?php

namespace App\Filament\Tenant\Resources\StudentBillResource\Pages;

use App\Filament\Tenant\Resources\StudentBillResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStudentBills extends ListRecords
{
    protected static string $resource = StudentBillResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
