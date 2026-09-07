<?php

namespace App\Filament\Widgets;

use App\Enums\Landlord\TenantStatus;
use App\Models\Landlord\Tenant;
use Filament\Widgets\ChartWidget;

class TenantsGrowthChartWidget extends ChartWidget
{
    protected static ?int $sort = 3;

    protected ?string $heading = 'Distribusi Status Tenant Kampus';

    protected ?string $maxHeight = '280px';

    protected function getData(): array
    {
        $active = Tenant::where('status', TenantStatus::ACTIVE)->count();
        $pending = Tenant::where('status', TenantStatus::PENDING)->count();
        $provisioning = Tenant::where('status', TenantStatus::PROVISIONING)->count();
        $suspended = Tenant::where('status', TenantStatus::SUSPENDED)->count();

        return [
            'datasets' => [
                [
                    'label' => 'Total Kampus',
                    'data' => [$active, $pending, $provisioning, $suspended],
                    'backgroundColor' => [
                        '#10B981', // green / active
                        '#F59E0B', // amber / pending
                        '#3B82F6', // blue / provisioning
                        '#EF4444', // red / suspended
                    ],
                ],
            ],
            'labels' => ['Aktif', 'Pending Approval', 'Provisioning', 'Suspended'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
