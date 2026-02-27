<?php

namespace App\Filament\Hr\Resources\Payrolls\Widgets;

use App\Models\Payroll;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class PayrollSummaryWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        // Get totals for the current month
        $summary = Payroll::query()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->select(
                DB::raw('SUM(gross_amount) as total_gross'),
                DB::raw('SUM(net_amount) as total_net'),
                DB::raw('COUNT(*) as total_count')
            )
            ->first();

        return [
            Stat::make(__('Monthly Gross Payroll'), number_format($summary->total_gross ?: 0, 0, ',', ' ').' HUF')
                ->description(__('Total gross amount in :month', ['month' => now()->translatedFormat('F')]))
                ->icon('heroicon-m-banknotes')
                ->color('primary'),
            Stat::make(__('Monthly Payslips'), $summary->total_count ?: 0)
                ->description(__('Count of generated payslips'))
                ->icon('heroicon-m-document-text')
                ->color('info'),
        ];
    }
}
