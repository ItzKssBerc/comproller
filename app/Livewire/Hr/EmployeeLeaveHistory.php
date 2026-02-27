<?php

namespace App\Livewire\Hr;

use App\Models\Employee;
use App\Models\Leave;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;

class EmployeeLeaveHistory extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    public Employee $employee;

    public function mount(Employee $employee)
    {
        $this->employee = $employee;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Leave::query()->where('employee_id', $this->employee->id))
            ->defaultSort('start_date', 'desc')
            ->columns([
                TextColumn::make('type')
                    ->label(__('Type'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'paid_leave' => __('Paid Leave'),
                        'sick_leave' => __('Sick Leave'),
                        'unpaid_leave' => __('Unpaid Leave'),
                        default => __('Other'),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'paid_leave' => 'success',
                        'sick_leave' => 'danger',
                        'unpaid_leave' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('start_date')
                    ->label(__('Start Date'))
                    ->date('Y. m. d.'),
                TextColumn::make('end_date')
                    ->label(__('End Date'))
                    ->date('Y. m. d.'),
                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => __('Pending'),
                        'approved' => __('Approved'),
                        'rejected' => __('Rejected'),
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->headerActions([
                Action::make('add_leave')
                    ->label(__('Add Leave'))
                    ->icon('heroicon-o-plus')
                    ->form([
                        Select::make('type')
                            ->label(__('Leave Type'))
                            ->options([
                                'paid_leave' => __('Paid Leave'),
                                'sick_leave' => __('Sick Leave'),
                                'unpaid_leave' => __('Unpaid Leave'),
                                'other' => __('Other'),
                            ])
                            ->required()
                            ->native(false),
                        DatePicker::make('start_date')
                            ->label(__('Start Date'))
                            ->required()
                            ->native(false),
                        DatePicker::make('end_date')
                            ->label(__('End Date'))
                            ->required()
                            ->native(false),
                        Textarea::make('reason')
                            ->label(__('Reason'))
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])
                    ->action(function (array $data): void {
                        Leave::create([
                            'employee_id' => $this->employee->id,
                            'type' => $data['type'],
                            'start_date' => $data['start_date'],
                            'end_date' => $data['end_date'],
                            'status' => 'approved',
                            'reason' => $data['reason'] ?? null,
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->title(__('Leave added successfully.'))
                            ->success()
                            ->send();
                    }),
            ])
            ->actions([
                Action::make('approve')
                    ->label(__('Approve'))
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (Leave $record): bool => $record->status === 'pending')
                    ->action(function (Leave $record): void {
                        $record->update(['status' => 'approved']);

                        \Filament\Notifications\Notification::make()
                            ->title(__('Leave approved.'))
                            ->success()
                            ->send();
                    }),
                Action::make('reject')
                    ->label(__('Reject'))
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn (Leave $record): bool => $record->status === 'pending')
                    ->form([
                        Textarea::make('reason')
                            ->label(__('Reason for rejection'))
                            ->required()
                            ->maxLength(65535),
                    ])
                    ->action(function (Leave $record, array $data): void {
                        $record->update([
                            'status' => 'rejected',
                            'reason' => $data['reason'],
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->title(__('Leave rejected.'))
                            ->danger()
                            ->send();
                    }),
            ])
            ->paginated(false);
    }

    public function render()
    {
        return view('livewire.hr.employee-leave-history');
    }
}
