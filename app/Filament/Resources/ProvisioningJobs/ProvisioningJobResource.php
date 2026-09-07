<?php

namespace App\Filament\Resources\ProvisioningJobs;

use App\Enums\Landlord\ProvisioningJobStatus;
use App\Filament\Resources\ProvisioningJobs\Pages\CreateProvisioningJob;
use App\Filament\Resources\ProvisioningJobs\Pages\EditProvisioningJob;
use App\Filament\Resources\ProvisioningJobs\Pages\ListProvisioningJobs;
use App\Filament\Resources\ProvisioningJobs\Schemas\ProvisioningJobForm;
use App\Filament\Resources\ProvisioningJobs\Tables\ProvisioningJobsTable;
use App\Models\Landlord\ProvisioningJob;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProvisioningJobResource extends Resource
{
    protected static ?string $model = ProvisioningJob::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::whereIn('status', [
            ProvisioningJobStatus::PENDING,
            ProvisioningJobStatus::RUNNING,
            ProvisioningJobStatus::PROVISIONING,
            ProvisioningJobStatus::PROCESSING,
        ])->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return ProvisioningJobForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProvisioningJobsTable::configure($table);
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Penyediaan & Operasional';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProvisioningJobs::route('/'),
            'create' => CreateProvisioningJob::route('/create'),
            'edit' => EditProvisioningJob::route('/{record}/edit'),
        ];
    }
}
