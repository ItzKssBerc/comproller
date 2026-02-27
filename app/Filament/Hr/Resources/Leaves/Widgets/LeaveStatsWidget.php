<?php

namespace App\Filament\Hr\Resources\Leaves\Widgets;

use App\Models\Leave;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class LeaveStatsWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $today = Carbon::today();

        // 1. How many people are on leave in this period?
        $onLeaveTodayCount = Leave::query()
            ->where('status', 'approved')
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->distinct('employee_id')
            ->count('employee_id');

        // 2. How many pending leave requests are there?
        $pendingLeavesCount = Leave::query()
            ->where('status', 'pending')
            ->count();

        // 3. How many people are on sick leave in this period?
        $sickLeaveTodayCount = Leave::query()
            ->where('status', 'approved')
            ->where('type', 'sick_leave')
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->distinct('employee_id')
            ->count('employee_id');

        return [
            Stat::make(__('On Leave Today'), $onLeaveTodayCount)
                ->description(__('Employees currently on approved leave'))
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make(__('On Sick Leave Today'), $sickLeaveTodayCount)
                ->description(__('Employees currently on sick leave'))
                ->descriptionIcon('heroicon-m-heart')
                ->color('danger'),

            Stat::make(__('Pending Requests'), $pendingLeavesCount)
                ->description(__('Leave requests waiting for approval'))
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingLeavesCount > 0 ? 'warning' : 'success'),
        ];
    }
}
