<?php

namespace App\Filament\Hr\Resources\Payrolls\Widgets;

use App\Models\Payroll;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class PayrollTrendChart extends ChartWidget
{
    protected int|string|array $columnSpan = 1;

    public function getHeading(): string
    {
        return __('Payroll Cost Trend');
    }

    protected function getData(): array
    {
        $data = Payroll::query()
            ->select(
                'period',
                DB::raw('SUM(gross_amount) as total_gross'),
                DB::raw('SUM(net_amount) as total_net')
            )
            ->groupBy('period')
            ->orderBy('period')
            ->latest()
            ->limit(12)
            ->get()
            ->reverse();

        return [
            'datasets' => [
                [
                    'label' => __('Gross Amount'),
                    'data' => $data->pluck('total_gross')->toArray(),
                    'backgroundColor' => '#2563eb', // blue
                ],
                [
                    'label' => __('Net Amount'),
                    'data' => $data->pluck('total_net')->toArray(),
                    'backgroundColor' => '#10b981', // green
                ],
            ],
            'labels' => $data->pluck('period')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
