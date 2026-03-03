<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $employee = Employee::first();
        if (! $employee) {
            return;
        }

        $startDate = Carbon::now()->subDays(30);

        for ($i = 0; $i < 30; $i++) {
            $currentDate = $startDate->copy()->addDays($i);

            // Skip weekends
            if ($currentDate->isWeekend()) {
                continue;
            }

            // Create attendance using factory
            Attendance::factory()->create([
                'employee_id' => $employee->id,
                'clock_in_at' => $currentDate->copy()->setHour(rand(7, 8))->setMinute(rand(0, 59)),
                'clock_out_at' => $currentDate->copy()->setHour(rand(16, 17))->setMinute(rand(0, 59)),
            ]);
        }
    }
}
