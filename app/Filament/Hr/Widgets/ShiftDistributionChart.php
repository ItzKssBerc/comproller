<?php

namespace App\Filament\Hr\Widgets;

use App\Models\EmployeeContract;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ShiftDistributionChart extends ChartWidget
{
    protected static ?int $sort = 2;

    public function getHeading(): string
    {
        return __('Shift Distribution');
    }

    protected function getData(): array
    {
        $data = EmployeeContract::query()
            ->select('shift', DB::raw('count(*) as total'))
            ->whereNotNull('shift')
            ->groupBy('shift')
            ->pluck('total', 'shift')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => __('Employees'),
                    'data' => array_values($data),
                    'backgroundColor' => [
                        '#3b82f6', // blue
                        '#10b981', // emerald
                        '#f59e0b', // amber
                        '#ef4444', // red
                        '#8b5cf6', // violet
                        '#ec4899', // pink
                    ],
                ],
            ],
            'labels' => array_keys($data),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
