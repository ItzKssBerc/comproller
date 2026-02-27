<?php

namespace App\Filament\Hr\Pages;

use BackedEnum;
use Filament\Pages\Page;

class Payroll extends Page
{
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-banknotes';

    protected string $view = 'filament.hr.pages.payroll';

    public function getTitle(): string
    {
        return __('Payroll');
    }

    public static function getNavigationLabel(): string
    {
        return __('Payroll');
    }
}
