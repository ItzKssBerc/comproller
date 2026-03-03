<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\EmployeePersonalData;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class EmployeeIdCardLanguageTest extends TestCase
{
    use RefreshDatabase;

    public function test_bulk_id_card_page_renders_current_language()
    {
        $user = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => \App\Enums\UserRole::Admin,
        ]);

        $employee = Employee::create(['is_active' => true]);
        EmployeePersonalData::create([
            'employee_id' => $employee->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        // Set locale to Hungarian
        App::setLocale('hu');
        $response = $this->actingAs($user)
            ->get(route('employees.bulk-id-card', ['ids' => [$employee->id]]));

        $response->assertStatus(200);
        $response->assertSee('HIVATALOS AZONOSÍTÓ');
        $response->assertDontSee('OFFICIAL IDENTIFICATION');

        // Set locale to English
        App::setLocale('en');
        $response = $this->actingAs($user)
            ->get(route('employees.bulk-id-card', ['ids' => [$employee->id]]));

        $response->assertSee('OFFICIAL IDENTIFICATION');
        $response->assertDontSee('HIVATALOS AZONOSÍTÓ');
    }

    public function test_single_id_card_page_respects_lang_parameter()
    {
        $user = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => \App\Enums\UserRole::Admin,
        ]);

        $employee = Employee::create(['is_active' => true]);
        EmployeePersonalData::create([
            'employee_id' => $employee->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        // Test Hungarian
        $response = $this->actingAs($user)
            ->get(route('employees.id-card', ['employee' => $employee, 'lang' => 'hu']));
        $response->assertSee('HIVATALOS AZONOSÍTÓ');
        $response->assertDontSee('OFFICIAL IDENTIFICATION');

        // Test English
        $response = $this->actingAs($user)
            ->get(route('employees.id-card', ['employee' => $employee, 'lang' => 'en']));
        $response->assertSee('OFFICIAL IDENTIFICATION');
        $response->assertDontSee('HIVATALOS AZONOSÍTÓ');
    }
}
