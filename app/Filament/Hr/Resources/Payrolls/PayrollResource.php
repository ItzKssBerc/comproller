<?php

namespace App\Filament\Hr\Resources\Payrolls;

use App\Filament\Hr\Resources\Payrolls\Pages\CreatePayroll;
use App\Filament\Hr\Resources\Payrolls\Pages\EditPayroll;
use App\Filament\Hr\Resources\Payrolls\Pages\ListPayrolls;
use App\Filament\Hr\Resources\Payrolls\Schemas\PayrollForm;
use App\Filament\Hr\Resources\Payrolls\Tables\PayrollsTable;
use App\Models\Payroll;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PayrollResource extends Resource
{
    protected static ?string $model = Payroll::class;

    protected static ?string $slug = 'payroll';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function getLabel(): string
    {
        return __('Payroll');
    }

    public static function getPluralLabel(): string
    {
        return __('Payroll');
    }

    public static function getNavigationLabel(): string
    {
        return __('Payroll');
    }

    public static function form(Schema $schema): Schema
    {
        return PayrollForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PayrollsTable::configure($table);
    }

    public static function getWidgets(): array
    {
        return [
            \App\Filament\Hr\Resources\Payrolls\Widgets\PayrollSummaryWidget::class,
            \App\Filament\Hr\Resources\Payrolls\Widgets\PayrollTrendChart::class,
            \App\Filament\Hr\Resources\Payrolls\Widgets\RecentPayrollsWidget::class,
            \App\Filament\Hr\Resources\Payrolls\Widgets\GeneratePayrollWidget::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPayrolls::route('/'),
            'create' => CreatePayroll::route('/create'),
            'edit' => EditPayroll::route('/{record}/edit'),
        ];
    }
}
