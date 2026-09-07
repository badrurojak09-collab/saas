<?php

namespace App\Filament\Widgets;

use App\Enums\Landlord\PlatformUserStatus;
use App\Enums\Landlord\ProvisioningJobStatus;
use App\Enums\Landlord\SubscriptionStatus;
use App\Enums\Landlord\TenantStatus;
use App\Models\Landlord\PlatformUser;
use App\Models\Landlord\ProvisioningJob;
use App\Models\Landlord\Tenant;
use App\Models\Landlord\TenantSubscription;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LandlordOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalTenants = Tenant::count();
        $activeTenants = Tenant::where('status', TenantStatus::ACTIVE)->count();

        $activeSubscriptions = TenantSubscription::where('status', SubscriptionStatus::ACTIVE)->count();

        $pendingJobs = ProvisioningJob::whereIn('status', [
            ProvisioningJobStatus::PENDING,
            ProvisioningJobStatus::PROCESSING,
        ])->count();

        $activePlatformUsers = PlatformUser::where('status', PlatformUserStatus::ACTIVE)->count();

        return [
            Stat::make('Total Tenant', (string) $totalTenants)
                ->description("{$activeTenants} aktif beroperasi")
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary'),

            Stat::make('Langganan Aktif', (string) $activeSubscriptions)
                ->description('Tenant berlangganan')
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('success'),

            Stat::make('Antrean Provisioning', (string) $pendingJobs)
                ->description($pendingJobs > 0 ? 'Sedang diproses' : 'Semua selesai')
                ->descriptionIcon('heroicon-m-cpu-chip')
                ->color($pendingJobs > 0 ? 'warning' : 'gray'),

            Stat::make('Platform Admin', (string) $activePlatformUsers)
                ->description('Pengguna landlord aktif')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('info'),
        ];
    }
}
