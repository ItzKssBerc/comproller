<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SetupTest extends TestCase
{
    use RefreshDatabase;

    public function test_setup_page_is_accessible_when_no_users_exist(): void
    {
        $response = $this->get('/setup');

        $response->assertStatus(200);
        $response->assertSee(__('Create Application Profile'));
    }

    public function test_can_create_admin_account_via_setup_and_verify_2fa(): void
    {
        $this->get('/setup');

        $response = $this->post('/setup', [
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/setup');
        $this->assertDatabaseHas('users', [
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => \App\Enums\UserRole::App->value,
        ]);

        $user = \App\Models\User::first();
        $this->assertNotNull($user->google2fa_secret);
        $response->assertSessionHas('setup_user_id', $user->id);

        $this->assertGuest();

        // Simulate step 2: Verify 2FA
        $google2fa = app('pragmarx.google2fa');
        $validCode = $google2fa->getCurrentOtp($user->google2fa_secret);

        $verifyResponse = $this->withSession(['setup_user_id' => $user->id])
            ->post('/setup/2fa', [
                'one_time_password' => $validCode,
            ]);

        $verifyResponse->assertRedirect('/admin');
        $this->assertAuthenticatedAs($user);
        $verifyResponse->assertSessionMissing('setup_user_id');
    }

    public function test_setup_validates_required_fields(): void
    {
        $response = $this->from('/setup')->post('/setup', []);

        $response->assertStatus(302);
        $response->assertRedirect('/setup');
        $response->assertSessionHasErrors(['name', 'email', 'password']);
    }

    public function test_setup_page_redirects_if_user_already_exists(): void
    {
        \App\Models\User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => \App\Enums\UserRole::Admin,
        ]);

        $response = $this->get('/setup');

        $response->assertRedirect('/admin');
    }
}
