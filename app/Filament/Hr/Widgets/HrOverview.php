<?php

namespace App\Filament\Hr\Widgets;

use App\Models\Employee;
use App\Models\EmployeeContract;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class HrOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected function getColumns(): int
    {
        return 3;
    }

    protected function getStats(): array
    {
        return [
            Stat::make(__('Total Employees'), Employee::count())
                ->description(__('Registered in the system'))
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),
            Stat::make(__('Total Shifts'), EmployeeContract::whereNotNull('shift')->distinct('shift')->count('shift'))
                ->description(__('Active work shifts'))
                ->descriptionIcon('heroicon-m-clock')
                ->color('success'),
            Stat::make(__('Total Positions'), EmployeeContract::whereNotNull('position')->distinct('position')->count('position'))
                ->description(__('Distinct roles defined'))
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('warning'),
        ];
    }
}
