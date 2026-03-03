<?php

namespace Tests\Feature;

use App\Models\User;
use Filament\Auth\Pages\Login;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class Login2FaTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_triggers_mfa_if_secret_exists(): void
    {
        $google2fa = app('pragmarx.google2fa');

        $user = User::create([
            'name' => 'Admin User',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
            'google2fa_secret' => $google2fa->generateSecretKey(),
            'role' => \App\Enums\UserRole::Admin,
        ]);

        Livewire::test(Login::class)
            ->fillForm([
                'email' => 'admin@admin.com',
                'password' => 'password',
            ])
            ->call('authenticate')
            ->assertHasNoErrors()
            ->assertSet('userUndertakingMultiFactorAuthentication', function ($value) use ($user) {
                return decrypt($value) == $user->getAuthIdentifier();
            });
    }

    public function test_2fa_verification_logs_user_in(): void
    {
        $google2fa = app('pragmarx.google2fa');
        $secret = $google2fa->generateSecretKey();

        $user = User::create([
            'name' => 'Admin User',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
            'google2fa_secret' => $secret,
            'role' => \App\Enums\UserRole::Admin,
        ]);

        $validCode = $google2fa->getCurrentOtp($secret);

        Livewire::test(Login::class)
            ->fillForm([
                'email' => 'admin@admin.com',
                'password' => 'password',
            ])
            ->call('authenticate')
            ->fillForm([
                'multiFactor.google_2fa.code' => $validCode,
            ])
            ->call('authenticate')
            ->assertRedirect(Filament::getUrl());

        $this->assertAuthenticatedAs($user);
    }

    public function test_2fa_verification_fails_with_invalid_code(): void
    {
        $google2fa = app('pragmarx.google2fa');

        $user = User::create([
            'name' => 'Admin User',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
            'google2fa_secret' => $google2fa->generateSecretKey(),
            'role' => \App\Enums\UserRole::Admin,
        ]);

        Livewire::test(Login::class)
            ->fillForm([
                'email' => 'admin@admin.com',
                'password' => 'password',
            ])
            ->call('authenticate')
            ->fillForm([
                'multiFactor.google_2fa.code' => '000000',
            ])
            ->call('authenticate')
            ->assertHasErrors(['data.multiFactor.google_2fa.code']);

        $this->assertGuest();
    }
}
