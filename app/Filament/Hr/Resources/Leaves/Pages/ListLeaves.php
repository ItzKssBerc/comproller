<?php

namespace App\Filament\Hr\Resources\Leaves\Pages;

use App\Filament\Hr\Resources\Leaves\LeaveResource;
use Filament\Resources\Pages\Page;

class ListLeaves extends Page
{
    protected static string $resource = LeaveResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Hr\Resources\Leaves\Widgets\LeaveStatsWidget::class,
            \App\Filament\Hr\Resources\Leaves\Widgets\LeaveCalendarWidget::class,
        ];
    }
}
