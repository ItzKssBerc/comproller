<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'is_active' => true,
            'locker_key' => null,
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Employee $employee) {
            $firstName = fake()->firstName();
            $lastName = fake()->lastName();

            $employee->personalData()->create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'date_of_birth' => fake()->date('Y-m-d', '-18 years'),
                'mothers_name' => fake()->name('female'),
            ]);

            $employee->contactData()->create([
                'phone' => fake()->phoneNumber(),
                'email' => fake()->unique()->safeEmail(),
                'address' => fake()->address(),
            ]);

            $employee->financialData()->create([
                'tax_number' => fake()->numerify('##########'),
                'social_security_number' => fake()->numerify('###-###-###'),
                'bank_account_number' => fake()->iban('HU'),
            ]);

            $employee->identification()->create([
                'citizenship' => 'Magyar',
                'id_card_number' => fake()->bothify('######??'),
                'identification_device' => 'qr_code',
                'qr_code_hash' => null, // Will be updated below
            ]);

            $position = fake()->randomElement(['Műszakvezető', 'Operátor', 'Takarító', 'Raktáros']);
            $shifts = ['A műszak', 'B műszak', 'C műszak', 'D műszak', 'E műszak'];

            $employee->contract()->create([
                'employment_type' => 'permanent',
                'employment_term' => 'indefinite',
                'position' => $position,
                'shift' => fake()->randomElement($shifts),
                'scheduled_activation_at' => now()->subMonths(rand(1, 24)),
            ]);

            $employee->salaryDetail()->create([
                'base_hourly_rate' => $position === 'Műszakvezető' ? '3500' : '2200',
            ]);

            // Update QR code hash based on generated data
            $employee->identification->update([
                'qr_code_hash' => $employee->generateQrCodeHash(),
            ]);
        });
    }
}
