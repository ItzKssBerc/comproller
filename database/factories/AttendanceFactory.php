<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attendance>
 */
class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $clockIn = fake()->dateTimeBetween('-1 month', 'now');
        $clockOut = (clone $clockIn)->modify('+'.rand(7, 9).' hours');

        return [
            'employee_id' => Employee::factory(),
            'clock_in_at' => $clockIn,
            'clock_out_at' => $clockOut,
            'is_processed' => false,
        ];
    }
}
