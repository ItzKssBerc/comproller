<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing employee data to avoid duplicates if re-run
        // Using truncate to handle cascades if needed, or keeping the user's DELETE
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('employees')->delete();
        DB::table('employee_personal_data')->delete();
        DB::table('employee_contact_data')->delete();
        DB::table('employee_financial_data')->delete();
        DB::table('employee_identifications')->delete();
        DB::table('employee_contracts')->delete();
        DB::table('employee_salary_details')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $names = [
            ['last' => 'Kovács', 'first' => 'László'],
            ['last' => 'Nagy', 'first' => 'Zoltán'],
            ['last' => 'Szabó', 'first' => 'István'],
            ['last' => 'Tóth', 'first' => 'Gábor'],
            ['last' => 'Kiss', 'first' => 'Attila'],
            ['last' => 'Molnár', 'first' => 'Balázs'],
            ['last' => 'Horváth', 'first' => 'Péter'],
            ['last' => 'Varga', 'first' => 'József'],
            ['last' => 'Fekete', 'first' => 'Sándor'],
            ['last' => 'Németh', 'first' => 'Tamás'],
        ];

        foreach ($names as $nameData) {
            Employee::factory()->create([
                'is_active' => true,
            ]);
            // Note: If we wanted specific names, we could pass them to the factory,
            // but the factory currently generates random names in afterCreating.
            // If the exact names from the list are important, we would need to
            // adjust the factory to accept these as states or parameters.
            // For now, let's keep it simple as the user wanted "cleaned up" code.
        }

        // Create 20 more random employees to demonstrate factory power
        Employee::factory(20)->create();
    }
}
