<?php

namespace App\Filament\Auth\MultiFactor;

use Closure;
use Filament\Auth\MultiFactor\Contracts\MultiFactorAuthenticationProvider;
use Filament\Forms\Components\TextInput;
use Illuminate\Contracts\Auth\Authenticatable;

class Google2FaAuthenticationProvider implements MultiFactorAuthenticationProvider
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'google_2fa';
    }

    public function getLoginFormLabel(): string
    {
        return 'Google Authenticator';
    }

    public function isEnabled(Authenticatable $user): bool
    {
        /** @var \App\Models\User $user */
        return !empty($user->google2fa_secret);
    }

    public function getManagementSchemaComponents(): array
    {
        return [];
    }

    public function getChallengeFormComponents(Authenticatable $user): array
    {
        return [
            TextInput::make('code')
                ->label('Hitelesítő kód')
                ->placeholder('000000')
                ->maxLength(6)
                ->inputMode('numeric')
                ->required()
                ->extraInputAttributes([
                    'class' => 'text-center tracking-[0.5em] font-mono text-xl',
                    'autocomplete' => 'one-time-code',
                ])
                ->rule(static function () use ($user): Closure {
                    return static function (string $attribute, $value, Closure $fail) use ($user): void {
                        $google2fa = app('pragmarx.google2fa');

                        /** @var \App\Models\User $user */
                        if ($google2fa->verifyKey($user->google2fa_secret, $value)) {
                            return;
                        }

                        $fail('Érvénytelen vagy lejárt azonosító kód.');
                    };
                }),
        ];
    }
}
