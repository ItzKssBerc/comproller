<?php

namespace App\Filament\Hr\Resources\Leaves\Widgets;

use App\Models\Leave;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class LeaveTypeChart extends ChartWidget
{
    protected ?string $heading = 'Leave Type Distribution';

    public function getHeading(): string
    {
        return __('Leave Type Distribution');
    }

    protected function getData(): array
    {
        $data = Leave::query()
            ->select('type', DB::raw('count(*) as count'))
            ->groupBy('type')
            ->get();

        $labels = $data->map(fn ($item) => __($item->type))->toArray();
        $counts = $data->pluck('count')->toArray();

        // Map types to colors
        $colors = $data->map(fn ($item) => match ($item->type) {
            'paid_leave' => '#10b981', // green
            'sick_leave' => '#ef4444', // red
            'unpaid_leave' => '#f59e0b', // amber
            default => '#6b7280', // gray
        })->toArray();

        return [
            'datasets' => [
                [
                    'label' => __('Leaves'),
                    'data' => $counts,
                    'backgroundColor' => $colors,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
