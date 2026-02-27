<?php

namespace App\Filament\Hr\Resources\Payrolls\Widgets;

use App\Models\Employee;
use App\Traits\HasPayrollGeneration;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Grid;
use Filament\Support\Enums\Size;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class GeneratePayrollWidget extends BaseWidget
{
    use HasPayrollGeneration;

    protected static ?string $heading = 'Payroll Console';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Employee::query()
                    ->where('is_active', true)
                    ->where(function ($query) {
                        $query->whereHas('attendances', function ($q) {
                            $q->where('is_processed', false);
                        })
                            ->orWhereHas('leaves', function ($q) {
                                $q->where('status', 'approved')
                                    ->where('is_processed', false);
                            })
                            ->orWhereHas('payrolls', function ($q) {
                                $q->whereMonth('created_at', now()->month)
                                    ->whereYear('created_at', now()->year)
                                    ->where('status', '!=', 'closed');
                            });
                    })
            )
            ->columns([
                TextColumn::make('personalData.last_name')
                    ->label(__('Employee Name'))
                    ->formatStateUsing(fn (Employee $record) => $record->personalData?->last_name.' '.$record->personalData?->first_name)
                    ->searchable(['last_name', 'first_name']),
                TextColumn::make('id')
                    ->label(__('Employee ID'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('unprocessed_count')
                    ->label(__('Number of Days'))
                    ->state(function (Employee $record) {
                        $payroll = $record->payrolls()->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->first();

                        if ($payroll) {
                            return $payroll->attendances()->count() + $payroll->leaves()->count();
                        }

                        $attendancesCount = $record->attendances()
                            ->where('is_processed', false)
                            ->count();

                        $leavesCount = $record->leaves()
                            ->where('status', 'approved')
                            ->where('is_processed', false)
                            ->count();

                        return $attendancesCount + $leavesCount;
                    })
                    ->badge()
                    ->color(function ($state, Employee $record): string {
                        $payroll = $record->payrolls()->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->exists();

                        return $payroll ? 'success' : 'warning';
                    }),
                TextColumn::make('is_processed_flag')
                    ->label(__('Processed'))
                    ->state(fn (Employee $record) => $record->payrolls()->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->exists() ? '1' : '0')
                    ->formatStateUsing(fn (string $state) => $state === '1' ? __('Feldolgozva') : __('Nincs feldolgozva'))
                    ->badge()
                    ->color(fn (string $state) => $state === '1' ? 'success' : 'danger'),
            ])
            ->actions([
                $this->getGeneratePayrollAction()
                    ->label(__('Generate Payroll'))
                    ->disabled(function (Employee $record) {
                        $attendancesCount = $record->attendances()
                            ->where('is_processed', false)
                            ->count();

                        $leavesCount = $record->leaves()
                            ->where('status', 'approved')
                            ->where('is_processed', false)
                            ->count();

                        return ($attendancesCount + $leavesCount) === 0;
                    }),

                Action::make('forward')
                    ->label(__('Forward'))
                    ->icon('heroicon-o-paper-airplane')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->disabled(function (Employee $record) {
                        $payroll = $record->payrolls()->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->first();

                        return ! $payroll || $payroll->is_forwarded;
                    })
                    ->action(function (Employee $record) {
                        $payroll = $record->payrolls()->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->first();
                        if ($payroll) {
                            $payroll->update([
                                'is_forwarded' => true,
                                'status' => 'closed',
                            ]);
                            \Filament\Notifications\Notification::make()
                                ->title(__('Payroll forwarded successfully'))
                                ->success()
                                ->send();
                        }
                    }),

                Action::make('view')
                    ->label(__('View History'))
                    ->icon('heroicon-o-clock')
                    ->color('gray')
                    ->size(Size::Small)
                    ->modalHeading(fn (Employee $record) => __('Payroll History for :name', ['name' => $record->personalData?->last_name.' '.$record->personalData?->first_name]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel(__('Close'))
                    ->slideOver()
                    ->infolist(
                        fn (Employee $record): array => [
                            RepeatableEntry::make('payrolls')
                                ->getStateUsing(fn (Employee $record) => $record->payrolls()->latest()->get())
                                ->label(__('Previous Payrolls'))
                                ->schema([
                                    Grid::make(4)
                                        ->schema([
                                            TextEntry::make('employee_id')
                                                ->label(__('ID'))
                                                ->color('gray'),
                                            TextEntry::make('period')
                                                ->label(__('Month'))
                                                ->weight('bold'),
                                            TextEntry::make('gross_amount')
                                                ->label(__('Gross Amount'))
                                                ->money('HUF')
                                                ->color('success'),
                                            TextEntry::make('net_amount')
                                                ->label(__('Net Amount'))
                                                ->money('HUF')
                                                ->color('primary')
                                                ->weight('bold'),
                                        ]),
                                    Actions::make([
                                        Action::make('recalculate')
                                            ->label(__('Recalculate'))
                                            ->icon('heroicon-o-arrow-path')
                                            ->color('warning')
                                            ->requiresConfirmation()
                                            ->action(fn ($record) => $this->recalculatePayroll($record)),
                                        Action::make('download')
                                            ->label(__('Download Payslip'))
                                            ->icon('heroicon-o-arrow-down-tray')
                                            ->color('primary')
                                            ->action(function ($record) {
                                                return redirect()->to(route('payrolls.download', [
                                                    'payroll' => $record,
                                                    'locale' => app()->getLocale(),
                                                ]));
                                            }),
                                    ])->alignRight(),
                                ]),
                        ]
                    ),
            ])
            ->bulkActions([
                BulkAction::make('bulk_generate')
                    ->label(__('Bulk Generate Payroll'))
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                        $processedCount = 0;
                        $skippedCount = 0;
                        $lastMonth = now()->subMonth();

                        foreach ($records as $employee) {
                            $attendanceIds = $employee->attendances()
                                ->where('is_processed', false)
                                ->whereMonth('clock_in_at', $lastMonth->month)
                                ->whereYear('clock_in_at', $lastMonth->year)
                                ->pluck('id')
                                ->toArray();

                            $leaveIds = $employee->leaves()
                                ->where('status', 'approved')
                                ->where('is_processed', false)
                                ->whereMonth('start_date', $lastMonth->month)
                                ->whereYear('start_date', $lastMonth->year)
                                ->pluck('id')
                                ->toArray();

                            $penaltyIds = $employee->penalties()
                                ->where('is_processed', false)
                                ->pluck('id')
                                ->toArray();

                            if (empty($attendanceIds) && empty($leaveIds)) {
                                $skippedCount++;

                                continue;
                            }

                            if (! empty($attendanceIds) || ! empty($leaveIds) || ! empty($penaltyIds)) {
                                $this->generatePayroll($employee, $attendanceIds, $leaveIds, $penaltyIds);
                                $processedCount++;
                            }
                        }

                        if ($processedCount === 0) {
                            \Filament\Notifications\Notification::make()
                                ->title(__('No eligible data to process for selected employees'))
                                ->warning()
                                ->send();
                        } else {
                            $message = __(':count payroll(s) generated successfully.', ['count' => $processedCount]);
                            if ($skippedCount > 0) {
                                $message .= ' '.__(':skipped skipped (0 days).', ['skipped' => $skippedCount]);
                            }
                            \Filament\Notifications\Notification::make()
                                ->title($message)
                                ->success()
                                ->send();
                        }
                    })
                    ->deselectRecordsAfterCompletion(),

                BulkAction::make('bulk_forward')
                    ->label(__('Bulk Forward'))
                    ->icon('heroicon-o-paper-airplane')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                        $forwardedCount = 0;
                        foreach ($records as $employee) {
                            $payroll = $employee->payrolls()->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->first();
                            if ($payroll && ! $payroll->is_forwarded) {
                                $payroll->update([
                                    'is_forwarded' => true,
                                    'status' => 'closed',
                                ]);
                                $forwardedCount++;
                            }
                        }

                        if ($forwardedCount > 0) {
                            \Filament\Notifications\Notification::make()
                                ->title(__(':count payroll(s) forwarded successfully', ['count' => $forwardedCount]))
                                ->success()
                                ->send();
                        } else {
                            \Filament\Notifications\Notification::make()
                                ->title(__('No payrolls to forward for selected employees'))
                                ->warning()
                                ->send();
                        }
                    })
                    ->deselectRecordsAfterCompletion(),
            ]);
    }
}
