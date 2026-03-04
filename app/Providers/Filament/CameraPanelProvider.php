<?php

namespace App\Providers\Filament;

use App\Enums\UserRole;
use App\Filament\Camera\Pages\Dashboard;
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

class CameraPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('camera')
            ->path('camera')
            ->brandName(CompanySettings::name())
            ->login(\App\Filament\Auth\CustomLogin::class)
            ->multiFactorAuthentication([
                \App\Filament\Auth\MultiFactor\Google2FaAuthenticationProvider::make(),
            ])
            ->colors([
                'primary' => Color::Indigo,
            ])
            ->discoverResources(in: app_path('Filament/Camera/Resources'), for: 'App\\Filament\\Camera\\Resources')
            ->discoverPages(in: app_path('Filament/Camera/Pages'), for: 'App\\Filament\\Camera\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Camera/Widgets'), for: 'App\\Filament\\Camera\\Widgets')
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
            ->authMiddleware([
                FilamentAuthenticate::class,
                EnsureUserHasRole::class.':'.UserRole::Camera->value,
            ]);
    }
}