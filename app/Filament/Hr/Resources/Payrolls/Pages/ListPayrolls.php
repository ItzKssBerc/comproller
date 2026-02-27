<?php

namespace App\Filament\Hr\Resources\Payrolls\Pages;

use App\Filament\Hr\Resources\Payrolls\PayrollResource;
use Filament\Resources\Pages\Page;

class ListPayrolls extends Page
{
    use \App\Traits\HasPayrollGeneration;

    protected static string $resource = PayrollResource::class;

    protected static ?string $title = 'Payroll';

    protected string $view = 'filament.hr.resources.payrolls.pages.list-payrolls';

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Hr\Resources\Payrolls\Widgets\PayrollSummaryWidget::class,
            \App\Filament\Hr\Resources\Payrolls\Widgets\PayrollTrendChart::class,
            \App\Filament\Hr\Resources\Payrolls\Widgets\GeneratePayrollWidget::class,
            \App\Filament\Hr\Resources\Payrolls\Widgets\RecentPayrollsWidget::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 2;
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }
}
