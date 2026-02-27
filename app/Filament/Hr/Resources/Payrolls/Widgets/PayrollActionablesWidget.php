<?php

namespace App\Filament\Hr\Resources\Payrolls\Widgets;

use Filament\Widgets\Widget;

class PayrollActionablesWidget extends Widget
{
    protected string $view = 'filament.hr.resources.payrolls.widgets.payroll-actionables-widget';

    protected int|string|array $columnSpan = 'full';
}
