<?php

namespace App\Providers\Filament;

use App\Enums\UserRole;
use App\Filament\Hr\Pages\Dashboard;
use App\Http\Middleware\EnsureUserHasRole;
use App\Http\Middleware\FilamentAuthenticate;
use App\Support\CompanySettings;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class HrPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('hr')
            ->path('hr')
            ->brandName(CompanySettings::name())
            ->login(\App\Filament\Auth\CustomLogin::class)
            ->multiFactorAuthentication([
                \App\Filament\Auth\MultiFactor\Google2FaAuthenticationProvider::make(),
            ])
            ->colors([
                'primary' => Color::Indigo,
            ])
            ->discoverResources(in: app_path('Filament/Hr/Resources'), for: 'App\\Filament\\Hr\\Resources')
            ->discoverPages(in: app_path('Filament/Hr/Pages'), for: 'App\\Filament\\Hr\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->userMenuItems([
                \Filament\Navigation\MenuItem::make()
                    ->label(__('Settings'))
                    ->url(fn (): string => \App\Filament\Hr\Pages\Settings::getUrl())
                    ->icon('heroicon-o-cog-6-tooth'),
            ])
            ->discoverWidgets(in: app_path('Filament/Hr/Widgets'), for: 'App\\Filament\\Hr\\Widgets')
            ->widgets([])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                \App\Http\Middleware\SetLocale::class,
            ])
            ->plugins([
                \Saade\FilamentFullCalendar\FilamentFullCalendarPlugin::make()
                    ->selectable(true),
            ])
            ->authMiddleware([
                FilamentAuthenticate::class,
                EnsureUserHasRole::class.':'.UserRole::HR->value,
            ]);
    }
}