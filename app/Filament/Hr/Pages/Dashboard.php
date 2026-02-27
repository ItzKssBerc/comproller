<?php

namespace App\Filament\Hr\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function getTitle(): string
    {
        return __('Information');
    }

    public function getWidgets(): array
    {
        return [
            \App\Filament\Hr\Widgets\HrOverview::class,
            \App\Filament\Hr\Widgets\ShiftDistributionChart::class,
            \App\Filament\Hr\Widgets\AttendanceMonitor::class,
        ];
    }
}
