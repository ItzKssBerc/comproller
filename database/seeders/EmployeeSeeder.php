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
        DB::statement('DELETE FROM employees');

        $shifts = ['A műszak', 'B műszak', 'C műszak', 'D műszak', 'E műszak'];
        
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

        foreach ($names as $index => $nameData) {
            $employee = Employee::create([
                'is_active' => true,
            ]);

            $employee->personalData()->create([
                'first_name' => $nameData['first'],
                'last_name' => $nameData['last'],
                'date_of_birth' => rand(1970, 2000) . '-' . str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT) . '-' . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT),
                'mothers_name' => 'Teszt Mária',
            ]);

            $employee->contactData()->create([
                'phone' => '+3630' . rand(1000000, 9999999),
                'email' => strtolower($nameData['last'] . '.' . $nameData['first']) . '@example.com',
                'address' => '1234 Budapest, Példa utca ' . ($index + 1) . '.',
            ]);

            $employee->financialData()->create([
                'tax_number' => rand(10000000, 99999999) . '-1-' . rand(10, 99),
                'social_security_number' => rand(100, 999) . '-' . rand(100, 999) . '-' . rand(100, 999),
                'bank_account_number' => '11773016-' . rand(10000000, 99999999) . '-00000000',
            ]);

            $employee->identification()->create([
                'citizenship' => 'Magyar',
                'id_card_number' => rand(100000, 999999) . 'AB',
                'identification_device' => 'qr_code',
            ]);

            // Assign shift and position
            // Every even index is a Shift Leader, every odd is an Operator
            // Every two employees share the same shift
            $shiftIndex = floor($index / 2);
            $position = ($index % 2 === 0) ? 'Műszakvezető' : 'Operátor';

            $employee->contract()->create([
                'employment_type' => 'permanent',
                'employment_term' => 'indefinite',
                'position' => $position,
                'shift' => $shifts[$shiftIndex],
                'scheduled_activation_at' => now()->subMonths(rand(1, 24)),
            ]);

            $employee->salaryDetail()->create([
                'base_hourly_rate' => ($position === 'Műszakvezető' ? '3500' : '2200'),
            ]);
            
            // Set the QR code hash based on the generated data
            $employee->identification->update([
                'qr_code_hash' => $employee->generateQrCodeHash()
            ]);
        }
    }
}
