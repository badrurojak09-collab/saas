<?php

namespace App\Providers\Filament;

use App\Filament\Tenant\Pages\Dashboard;
use App\Filament\Tenant\Widgets\RecentStudentsWidget;
use App\Filament\Tenant\Widgets\TenantOverviewWidget;
use App\Filament\Tenant\Widgets\TodayLecturesWidget;
use App\Tenancy\Contracts\TenantManager;
use App\Tenancy\Middleware\InitializeTenant;
use App\Tenancy\Middleware\PreventTenantLeakage;
use App\Tenancy\Middleware\ResolveTenant;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

final class TenantPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $panel = $panel
            ->id('tenant')
            ->path('tenant')
            ->login()
            ->authGuard('tenant')
            ->brandName(fn () => app(TenantManager::class)->current()?->name ?? 'SIAKAD Tenant')
            ->colors([
                'primary' => Color::Indigo,
                'gray' => Color::Slate,
                'success' => Color::Emerald,
                'danger' => Color::Rose,
                'warning' => Color::Amber,
                'info' => Color::Sky,
            ])
            ->sidebarCollapsibleOnDesktop()
            ->navigationGroups([
                NavigationGroup::make('Identitas & Akses')
                    ->icon('heroicon-o-identification'),
                NavigationGroup::make('Organisasi')
                    ->icon('heroicon-o-building-office-2'),
                NavigationGroup::make('Sivitas Akademika')
                    ->icon('heroicon-o-user-group'),
                NavigationGroup::make('Kalender & Periode')
                    ->icon('heroicon-o-calendar-days'),
                NavigationGroup::make('Kurikulum & Perkuliahan')
                    ->icon('heroicon-o-book-open'),
                NavigationGroup::make('Fasilitas & Ruang')
                    ->icon('heroicon-o-building-office'),
                NavigationGroup::make('Keuangan')
                    ->icon('heroicon-o-banknotes'),
            ])
            ->discoverResources(
                in: app_path('Filament/Tenant/Resources'),
                for: 'App\Filament\Tenant\Resources',
            )
            ->discoverPages(
                in: app_path('Filament/Tenant/Pages'),
                for: 'App\Filament\Tenant\Pages',
            )
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(
                in: app_path('Filament/Tenant/Widgets'),
                for: 'App\Filament\Tenant\Widgets',
            )
            ->widgets([
                TenantOverviewWidget::class,
                RecentStudentsWidget::class,
                TodayLecturesWidget::class,
                AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                ResolveTenant::class,
                InitializeTenant::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                PreventTenantLeakage::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->persistentMiddleware([
                ResolveTenant::class,
                InitializeTenant::class,
                PreventTenantLeakage::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);

        if ($tenantDomain = config('tenancy.panel_domain')) {
            $panel->tenantDomain($tenantDomain);
        }

        return $panel;
    }
}
