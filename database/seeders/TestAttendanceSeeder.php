<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TestAttendanceSeeder extends Seeder
{
    public function run(): void
    {
        // Clear all existing attendances first
        Attendance::query()->delete();

        foreach (Employee::all() as $employee) {
            // December 2025 data (10-14)
            for ($i = 10; $i <= 14; $i++) {
                $this->createAttendance($employee, 2025, 12, $i);
            }
        }
    }

    protected function createAttendance(Employee $employee, int $year, int $month, int $day): void
    {
        $clockIn = Carbon::create($year, $month, $day, rand(6, 9), 0, 0);

        // Randomly create auto-closed shifts (12h) to test editing
        $isAutoClosed = rand(1, 10) > 8;
        $clockOut = $isAutoClosed ? (clone $clockIn)->addHours(12) : (clone $clockIn)->addHours(rand(7, 9));

        Attendance::create([
            'employee_id' => $employee->id,
            'clock_in_at' => $clockIn,
            'clock_out_at' => $clockOut,
            'is_processed' => false,
        ]);
    }
}
