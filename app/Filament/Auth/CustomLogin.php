<?php

namespace App\Filament\Auth;

use App\Enums\UserRole;
use Filament\Actions\Action;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Facades\Filament;
use Filament\Forms\Components\Checkbox;
use Filament\Schemas\Components\Component;
use Filament\Forms\Components\TextInput;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class CustomLogin extends BaseLogin
{
    protected static string $layout = 'components.layouts.auth-split';

    protected string $view = 'filament.auth.custom-login';

    public function mount(): void
    {
        parent::mount();

        if (Session::has('locale')) {
            App::setLocale(Session::get('locale'));
        }
    }

    public function hasLogo(): bool
    {
        return false;
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('Email cím')
            ->email()
            ->required()
            ->autocomplete()
            ->autofocus();
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label('Jelszó')
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->autocomplete('current-password')
            ->required();
    }

    protected function getRememberFormComponent(): Component
    {
        return Checkbox::make('remember')
            ->label('Emlékezz rám');
    }

    protected function getAuthenticateFormAction(): Action
    {
        return parent::getAuthenticateFormAction()
            ->label('Bejelentkezés');
    }

    protected function getMultiFactorAuthenticateFormAction(): Action
    {
        return parent::getMultiFactorAuthenticateFormAction()
            ->label('Megerősítés és belépés');
    }

    protected function getRedirectUrl(): string
    {
        $user = auth()->user();

        $panelId = match ($user?->role) {
            UserRole::Admin => 'admin',
            UserRole::HR => 'hr',
            UserRole::Camera => 'camera',
            UserRole::Finance => 'finance',
            default => 'app',
        };

        return Filament::getPanel($panelId)->getUrl();
    }
}
