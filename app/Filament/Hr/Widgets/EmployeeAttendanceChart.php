<?php

namespace App\Filament\Hr\Widgets;

use App\Models\Attendance;
use App\Models\Employee;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Model;

class EmployeeAttendanceChart extends ChartWidget
{
    public ?Model $record = null;

    public function getHeading(): string
    {
        return __('Average Clock In/Out Times');
    }

    protected function getData(): array
    {
        if (!$this->record) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        $attendances = Attendance::where('employee_id', $this->record->id)
            ->whereNotNull('clock_in_at')
            ->whereNotNull('clock_out_at')
            ->get()
            ->groupBy(fn($attendance) => $attendance->clock_in_at->format('N'));

        $days = [
            1 => __('Monday'),
            2 => __('Tuesday'),
            3 => __('Wednesday'),
            4 => __('Thursday'),
            5 => __('Friday'),
        ];

        $clockInAverages = [];
        $clockOutAverages = [];
        $labels = [];

        foreach ($days as $dayNum => $dayName) {
            $dayAttendances = $attendances->get($dayNum, collect());
            
            if ($dayAttendances->isEmpty()) {
                $clockInAverages[] = null;
                $clockOutAverages[] = null;
            } else {
                $avgInMinutes = $dayAttendances->avg(fn($a) => $a->clock_in_at->hour * 60 + $a->clock_in_at->minute);
                $avgOutMinutes = $dayAttendances->avg(fn($a) => $a->clock_out_at->hour * 60 + $a->clock_out_at->minute);
                
                $clockInAverages[] = round($avgInMinutes / 60, 2);
                $clockOutAverages[] = round($avgOutMinutes / 60, 2);
            }
            $labels[] = $dayName;
        }

        return [
            'datasets' => [
                [
                    'label' => __('Clock In'),
                    'data' => $clockInAverages,
                    'borderColor' => '#10b981',
                    'backgroundColor' => '#10b981',
                    'fill' => false,
                    'tension' => 0.1,
                ],
                [
                    'label' => __('Clock Out'),
                    'data' => $clockOutAverages,
                    'borderColor' => '#ef4444',
                    'backgroundColor' => '#ef4444',
                    'fill' => false,
                    'tension' => 0.1,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<JS
        {
            scales: {
                y: {
                    min: 0,
                    max: 24,
                    ticks: {
                        stepSize: 1,
                        callback: (value) => value + ':00',
                    },
                },
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                let hours = Math.floor(context.parsed.y);
                                let minutes = Math.round((context.parsed.y - hours) * 60);
                                label += hours + ':' + (minutes < 10 ? '0' : '') + minutes;
                            }
                            return label;
                        },
                    },
                },
            },
        }
JS);
    }
}
