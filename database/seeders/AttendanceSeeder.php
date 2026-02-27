<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $employee = Employee::first();
        if (!$employee) {
            return;
        }

        $startDate = Carbon::now()->subDays(30);
        
        for ($i = 0; $i < 30; $i++) {
            $currentDate = $startDate->copy()->addDays($i);
            
            // Skip weekends
            if ($currentDate->isWeekend()) {
                continue;
            }

            // Random clock in between 07:30 and 09:30
            $clockIn = $currentDate->copy()->setHour(rand(7, 8))->setMinute(rand(0, 59));
            if ($clockIn->hour === 7 && $clockIn->minute < 30) {
                $clockIn->setMinute(rand(30, 59));
            }

            // Random clock out between 16:00 and 18:00
            $clockOut = $currentDate->copy()->setHour(rand(16, 17))->setMinute(rand(0, 59));

            Attendance::create([
                'employee_id' => $employee->id,
                'clock_in_at' => $clockIn,
                'clock_out_at' => $clockOut,
            ]);
        }
    }
}
