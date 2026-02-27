<?php

namespace App\Traits;

use App\Models\Payroll;
use Illuminate\Support\Str;

trait HasPayrollPdfData
{
    protected function preparePayrollData(Payroll $payroll): array
    {
        $payroll->loadMissing([
            'employee.personalData',
            'employee.contract',
            'employee.salaryDetail',
            'attendances',
            'leaves',
            'penalties',
        ]);

        $employee = $payroll->employee;
        $baseHourlyRate = (float) $employee->salaryDetail?->base_hourly_rate ?: 0;

        $attendances = $payroll->attendances->map(function ($a) use ($baseHourlyRate) {
            $duration = $a->clock_out_at ? round($a->clock_in_at->diffInMinutes($a->clock_out_at) / 60, 2) : 0;

            return [
                'date' => $a->clock_in_at,
                'label' => __('Attendance'),
                'duration' => $duration,
                'daily_wage' => round($duration * $baseHourlyRate),
                'is_weekend' => $a->clock_in_at->isWeekend(),
            ];
        });

        $leaves = collect();
        foreach ($payroll->leaves as $l) {
            $current = $l->start_date->copy();
            while ($current->lte($l->end_date)) {
                $duration = $current->isWeekend() || $l->type === 'unpaid_leave' ? 0 : 8;
                $leaves->push([
                    'date' => $current->copy(),
                    'label' => Str::of($l->type)->replace('_', ' ')->ucfirst(),
                    'duration' => $duration,
                    'daily_wage' => round($duration * $baseHourlyRate),
                    'is_weekend' => $current->isWeekend(),
                ]);
                $current->addDay();
            }
        }

        $details = $attendances->concat($leaves)->sortBy('date')->values();

        return [
            'payroll' => $payroll,
            'employee' => $employee,
            'details' => $details,
        ];
    }
}
