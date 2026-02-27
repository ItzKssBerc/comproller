<?php

namespace App\Traits;

use App\Models\Employee;
use App\Models\Payroll;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TimePicker;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\Size;
use Filament\Support\Enums\Width;
use Illuminate\Support\Str;

trait HasPayrollGeneration
{
    public function getGeneratePayrollAction(): Action
    {
        return Action::make('generatePayroll')
            ->label(__('Create'))
            ->color('success')
            ->icon('heroicon-o-plus-circle')
            ->size(Size::Small)
            ->modalHeading(function (?Employee $record = null) {
                $employee = $record ?? ($this->employee ?? null);

                return __('Create Payroll for :name', ['name' => $employee?->personalData?->last_name.' '.$employee?->personalData?->first_name]);
            })
            ->modalDescription(function (?Employee $record = null) {
                $employee = $record ?? ($this->employee ?? null);
                $rate = $employee?->salaryDetail?->base_hourly_rate ? number_format($employee->salaryDetail->base_hourly_rate, 0, '.', ' ').' HUF/h' : __('N/A');

                return __('Review un-processed attendances for the current month before creating the payroll.').' - '.__('Base Hourly Rate: :rate', ['rate' => $rate]);
            })
            ->modalSubmitActionLabel(__('Create Payroll'))
            ->slideOver()
            ->modalWidth(Width::SevenExtraLarge)
            ->fillForm(function (?Employee $record = null): array {
                $employee = $record ?? ($this->employee ?? null);
                if (! $employee) {
                    return [];
                }

                $lastMonth = now()->subMonth();

                return [
                    'attendances_data' => collect()
                        ->concat(
                            $employee->attendances()
                                ->where('is_processed', false)
                                ->get()
                                ->map(fn ($a) => [
                                    'type' => 'attendance',
                                    'id' => $a->id,
                                    'date_in' => $a->clock_in_at?->toDateString(),
                                    'date_out' => $a->clock_out_at?->toDateString(),
                                    'clock_in_at' => $a->clock_in_at?->format('H:i:s'),
                                    'clock_out_at' => $a->clock_out_at?->format('H:i:s'),
                                    'is_selected' => true,
                                ])
                        )
                        ->concat(
                            $employee->leaves()
                                ->where('status', 'approved')
                                ->where('is_processed', false)
                                ->get()
                                ->map(fn ($l) => [
                                    'type' => 'leave',
                                    'id' => $l->id,
                                    'date_in' => $l->start_date?->toDateString(),
                                    'date_out' => $l->end_date?->toDateString(),
                                    'leave_type' => $l->type,
                                    'is_selected' => true,
                                ])
                        )
                        ->values()
                        ->toArray(),
                    'penalties_data' => $employee->penalties()
                        ->where('is_processed', false)
                        ->get()
                        ->map(fn ($p) => [
                            'id' => $p->id,
                            'type' => $p->type,
                            'amount' => $p->amount,
                            'description' => $p->description,
                            'is_selected' => true,
                        ])
                        ->toArray(),
                ];
            })
            ->form([
                Repeater::make('attendances_data')
                    ->label(__('Un-processed Attendances & Leaves'))
                    ->schema([
                        Hidden::make('type'),
                        Hidden::make('id'),
                        Hidden::make('date_in'),
                        Hidden::make('date_out'),
                        Hidden::make('leave_type'),
                        Grid::make([
                            'default' => 1,
                            'md' => 12,
                        ])
                            ->schema([
                                Checkbox::make('is_selected')
                                    ->label(__('Include'))
                                    ->inline(false)
                                    ->live()
                                    ->columnSpan([
                                        'default' => 'full',
                                        'md' => 1,
                                    ]),
                                Placeholder::make('date_display')
                                    ->label(__('Date'))
                                    ->content(fn ($get) => \Carbon\Carbon::parse($get('date_in'))->format('Y. m. d. (l)'))
                                    ->columnSpan([
                                        'default' => 'full',
                                        'md' => 3,
                                    ]),
                                Placeholder::make('type_display')
                                    ->label(__('Type'))
                                    ->content(fn ($get) => Str::of($get('leave_type') ?? 'attendance')->replace('_', ' ')->ucfirst())
                                    ->columnSpan([
                                        'default' => 'full',
                                        'md' => 2,
                                    ]),
                                Grid::make([
                                    'default' => 1,
                                    'sm' => 2,
                                ])
                                    ->schema([
                                        TimePicker::make('clock_in_at')
                                            ->label(__('Clock In'))
                                            ->seconds(false)
                                            ->required()
                                            ->live()
                                            ->visible(fn ($get) => $get('type') === 'attendance'),
                                        TimePicker::make('clock_out_at')
                                            ->label(__('Clock Out'))
                                            ->seconds(false)
                                            ->required()
                                            ->live()
                                            ->visible(fn ($get) => $get('type') === 'attendance'),
                                        Placeholder::make('details_leave')
                                            ->label(__('Details'))
                                            ->content(__('Daily Leave'))
                                            ->visible(fn ($get) => $get('type') === 'leave'),
                                    ])
                                    ->columnSpan([
                                        'default' => 'full',
                                        'md' => 3,
                                    ]),
                                Placeholder::make('hours_display')
                                    ->label(__('Hours'))
                                    ->content(function ($get) {
                                        $hours = 0;
                                        if ($get('type') === 'attendance') {
                                            if ($get('clock_in_at') && $get('clock_out_at')) {
                                                $in = \Carbon\Carbon::parse($get('clock_in_at'));
                                                $out = \Carbon\Carbon::parse($get('clock_out_at'));
                                                $hours = round($in->diffInMinutes($out) / 60, 2);
                                            }
                                        } else {
                                            $start = \Carbon\Carbon::parse($get('date_in'));
                                            $end = \Carbon\Carbon::parse($get('date_out'));
                                            $days = 0;
                                            $current = $start->copy();
                                            while ($current->lte($end)) {
                                                if (! $current->isWeekend()) {
                                                    $days++;
                                                }
                                                $current->addDay();
                                            }
                                            $hours = $days * 8;
                                        }

                                        return $hours.' '.__('h');
                                    })
                                    ->extraAttributes(['class' => 'font-bold text-success-600'])
                                    ->columnSpan([
                                        'default' => 'full',
                                        'md' => 1,
                                    ]),
                                Placeholder::make('daily_amount')
                                    ->label(__('Amount'))
                                    ->content(function ($get, ?Employee $record = null) {
                                        $employee = $record ?? ($this->employee ?? null);
                                        $baseRate = (float) ($employee?->salaryDetail?->base_hourly_rate ?: 0);
                                        $hours = 0;

                                        if ($get('type') === 'attendance') {
                                            if ($get('clock_in_at') && $get('clock_out_at')) {
                                                $in = \Carbon\Carbon::parse($get('clock_in_at'));
                                                $out = \Carbon\Carbon::parse($get('clock_out_at'));
                                                $hours = round($in->diffInMinutes($out) / 60, 2);
                                            }
                                        } else {
                                            $start = \Carbon\Carbon::parse($get('date_in'));
                                            $end = \Carbon\Carbon::parse($get('date_out'));
                                            $days = 0;
                                            $current = $start->copy();
                                            while ($current->lte($end)) {
                                                if (! $current->isWeekend()) {
                                                    $days++;
                                                }
                                                $current->addDay();
                                            }
                                            $hours = $days * 8;
                                        }

                                        return number_format($hours * $baseRate, 0, '.', ' ').' HUF';
                                    })
                                    ->extraAttributes(['class' => 'font-bold text-primary-600'])
                                    ->columnSpan([
                                        'default' => 'full',
                                        'md' => 2,
                                    ]),
                            ]),
                    ])
                    ->columns(1)
                    ->addable(false)
                    ->deletable(false)
                    ->live(), // Important: Forces the form to update when checkboxes/times change
                Section::make(__('Payroll Summary'))
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 3,
                        ])
                            ->schema([
                                Placeholder::make('summary_attendance')
                                    ->label(__('Attendance Hours'))
                                    ->content(function ($get) {
                                        $data = $get('attendances_data');
                                        if (empty($data) || ! is_array($data)) {
                                            return '0 h';
                                        }

                                        $hours = collect($data)
                                            ->where('type', 'attendance')
                                            ->where('is_selected', true)
                                            ->sum(function ($a) {
                                                if (empty($a['clock_in_at']) || empty($a['clock_out_at'])) {
                                                    return 0;
                                                }

                                                try {
                                                    $in = \Carbon\Carbon::parse($a['clock_in_at']);
                                                    $out = \Carbon\Carbon::parse($a['clock_out_at']);

                                                    return round($in->diffInMinutes($out) / 60, 2);
                                                } catch (\Exception $e) {
                                                    return 0;
                                                }
                                            });

                                        return $hours.' h';
                                    }),
                                Placeholder::make('summary_paid_leave')
                                    ->label(__('Paid Leave Hours'))
                                    ->content(function ($get) {
                                        $data = $get('attendances_data');
                                        if (empty($data) || ! is_array($data)) {
                                            return '0 h';
                                        }

                                        $hours = collect($data)
                                            ->where('type', 'leave')
                                            ->where('leave_type', 'paid_leave')
                                            ->where('is_selected', true)
                                            ->sum(function ($l) {
                                                if (empty($l['date_in']) || empty($l['date_out'])) {
                                                    return 0;
                                                }

                                                try {
                                                    $start = \Carbon\Carbon::parse($l['date_in']);
                                                    $end = \Carbon\Carbon::parse($l['date_out']);
                                                    $days = 0;
                                                    $current = $start->copy();
                                                    while ($current->lte($end)) {
                                                        if (! $current->isWeekend()) {
                                                            $days++;
                                                        }
                                                        $current->addDay();
                                                    }

                                                    return $days * 8;
                                                } catch (\Exception $e) {
                                                    return 0;
                                                }
                                            });

                                        return $hours.' h';
                                    }),
                                Placeholder::make('summary_sick_leave')
                                    ->label(__('Sick Leave Hours'))
                                    ->content(function ($get) {
                                        $data = $get('attendances_data');
                                        if (empty($data) || ! is_array($data)) {
                                            return '0 h';
                                        }

                                        $hours = collect($data)
                                            ->where('type', 'leave')
                                            ->where('leave_type', 'sick_leave')
                                            ->where('is_selected', true)
                                            ->sum(function ($l) {
                                                if (empty($l['date_in']) || empty($l['date_out'])) {
                                                    return 0;
                                                }

                                                try {
                                                    $start = \Carbon\Carbon::parse($l['date_in']);
                                                    $end = \Carbon\Carbon::parse($l['date_out']);
                                                    $days = 0;
                                                    $current = $start->copy();
                                                    while ($current->lte($end)) {
                                                        if (! $current->isWeekend()) {
                                                            $days++;
                                                        }
                                                        $current->addDay();
                                                    }

                                                    return $days * 8;
                                                } catch (\Exception $e) {
                                                    return 0;
                                                }
                                            });

                                        return $hours.' h';
                                    }),
                            ]),
                    ])
                    ->columnSpanFull(),
                Repeater::make('penalties_data')
                    ->label(__('Un-processed Penalties'))
                    ->schema([
                        Hidden::make('id'),
                        Grid::make([
                            'default' => 1,
                            'sm' => 3,
                        ])
                            ->schema([
                                Checkbox::make('is_selected')
                                    ->label(__('Include'))
                                    ->inline(false),
                                Placeholder::make('penalty_type')
                                    ->label(__('Type'))
                                    ->content(fn ($get) => __($get('type') === 'lost_key' ? 'Lost Key' : ($get('type') === 'lost_rfid' ? 'Lost RFID Card' : ($get('type') === 'lost_qr' ? 'Lost QR Card' : 'Other Reason')))),
                                Placeholder::make('penalty_amount')
                                    ->label(__('Amount'))
                                    ->content(fn ($get) => number_format($get('amount'), 0, '.', ' ').' HUF'),
                            ]),
                    ])
                    ->addable(false)
                    ->deletable(false)
                    ->columnSpanFull()
                    ->visible(fn ($get) => count($get('penalties_data') ?? []) > 0),
            ])
            ->action(function (array $data, ?Employee $record = null) {
                $employee = $record ?? ($this->employee ?? null);
                if (! $employee) {
                    return;
                }

                // Save changes to attendances only
                foreach ($data['attendances_data'] as $item) {
                    if ($item['type'] === 'attendance') {
                        \App\Models\Attendance::find($item['id'])?->update([
                            'clock_in_at' => $item['date_in'].' '.$item['clock_in_at'],
                            'clock_out_at' => ($item['date_out'] ?? $item['date_in']).' '.$item['clock_out_at'],
                        ]);
                    }
                }

                $selectedAttendances = collect($data['attendances_data'])
                    ->where('type', 'attendance')
                    ->where('is_selected', true)
                    ->pluck('id')
                    ->toArray();

                $selectedLeaves = collect($data['attendances_data'])
                    ->where('type', 'leave')
                    ->where('is_selected', true)
                    ->pluck('id')
                    ->toArray();

                $selectedPenalties = collect($data['penalties_data'] ?? [])
                    ->where('is_selected', true)
                    ->pluck('id')
                    ->toArray();

                $this->generatePayroll($employee, $selectedAttendances, $selectedLeaves, $selectedPenalties);
            })
            ->hidden(function (Action $action, ?Employee $record = null) {
                $employee = $record ?? ($this->employee ?? null);

                if (! $employee) {
                    $livewire = $action->getLivewire();
                    if (method_exists($livewire, 'getRecord')) {
                        $employee = $livewire->getRecord();
                    }
                }

                if (! $employee instanceof Employee) {
                    return true;
                }

                return ! $employee->attendances()->where('is_processed', false)->exists() &&
                    ! $employee->leaves()->where('status', 'approved')->where('is_processed', false)->exists() &&
                    ! $employee->penalties()->where('is_processed', false)->exists();
            });
    }

    public function generatePayroll(Employee $employee, array $selectedAttendances = [], array $selectedLeaves = [], array $selectedPenalties = []): void
    {
        // Get selected un-processed attendances
        $attendances = $employee->attendances()
            ->whereIn('id', $selectedAttendances)
            ->where('is_processed', false)
            ->get();

        // Get selected approved un-processed leaves
        $leaves = $employee->leaves()
            ->whereIn('id', $selectedLeaves)
            ->where('status', 'approved')
            ->where('is_processed', false)
            ->get();

        // Get selected un-processed penalties
        $penalties = $employee->penalties()
            ->whereIn('id', $selectedPenalties)
            ->where('is_processed', false)
            ->get();

        if ($attendances->isEmpty() && $leaves->isEmpty() && $penalties->isEmpty()) {
            return;
        }

        // Calculate period range
        $allDates = $attendances->pluck('clock_in_at')->concat($leaves->pluck('start_date'))->concat($leaves->pluck('end_date'))->filter();
        $minDate = $allDates->min()?->format('Y. m. d.');
        $maxDate = $allDates->max()?->format('Y. m. d.');
        $periodRange = "{$minDate} - {$maxDate}";

        $baseRate = (float) $employee->salaryDetail?->base_hourly_rate ?: 0;

        // Sum hours
        $attendanceHours = $attendances->sum(fn ($a) => $a->clock_out_at ? $a->clock_in_at->diffInMinutes($a->clock_out_at) / 60 : 0);

        $paidLeaveHours = $leaves->where('type', 'paid_leave')->sum(function ($l) {
            $days = 0;
            $current = $l->start_date->copy();
            while ($current->lte($l->end_date)) {
                if (! $current->isWeekend()) {
                    $days++;
                }
                $current->addDay();
            }

            return $days * 8;
        });

        $sickLeaveHours = $leaves->where('type', 'sick_leave')->sum(function ($l) {
            $days = 0;
            $current = $l->start_date->copy();
            while ($current->lte($l->end_date)) {
                if (! $current->isWeekend()) {
                    $days++;
                }
                $current->addDay();
            }

            return $days * 8;
        });

        $totalHours = $attendanceHours + $paidLeaveHours + $sickLeaveHours;

        $gross = $totalHours * $baseRate;
        $totalDeductions = $penalties->sum('amount');
        $net = ($gross * 0.65) - $totalDeductions; // Dummy 35% tax minus deductions

        $payroll = Payroll::create([
            'employee_id' => $employee->id,
            'period' => $periodRange,
            'base_salary' => (float) $employee->salaryDetail?->base_hourly_rate * 160, // Standard 160h base
            'attendance_hours' => $attendanceHours,
            'paid_leave_hours' => $paidLeaveHours,
            'sick_leave_hours' => $sickLeaveHours,
            'total_hours' => $totalHours,
            'gross_amount' => $gross,
            'total_deductions' => $totalDeductions,
            'net_amount' => $net,
            'status' => 'completed',
        ]);

        // Mark processed
        foreach ($attendances as $a) {
            $a->update(['is_processed' => true, 'payroll_id' => $payroll->id]);
        }

        foreach ($leaves as $l) {
            $l->update(['is_processed' => true, 'payroll_id' => $payroll->id]);
        }

        foreach ($penalties as $p) {
            $p->update(['is_processed' => true, 'payroll_id' => $payroll->id]);
        }

        Notification::make()
            ->title(__('Payroll created successfully'))
            ->success()
            ->send();

        if (method_exists($this, 'dispatch')) {
            $this->dispatch('refreshTable');
        }
    }

    public function recalculatePayroll(Payroll $payroll): void
    {
        // Release linked attendances
        foreach ($payroll->attendances as $attendance) {
            $attendance->update([
                'is_processed' => false,
                'payroll_id' => null,
            ]);
        }

        // Release linked leaves
        foreach ($payroll->leaves as $leave) {
            $leave->update([
                'is_processed' => false,
                'payroll_id' => null,
            ]);
        }

        // Release linked penalties
        foreach ($payroll->penalties as $penalty) {
            $penalty->update([
                'is_processed' => false,
                'payroll_id' => null,
            ]);
        }

        // Delete the payroll record
        $payroll->delete();

        Notification::make()
            ->title(__('Payroll recalculated. Unprocessed items returned to queue.'))
            ->success()
            ->send();

        if (method_exists($this, 'dispatch')) {
            $this->dispatch('refreshTable');
        }
    }
}
