<?php

namespace App\Livewire\Hr;

use App\Models\Leave;
use Carbon\Carbon;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;

class LeaveDayTable extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    public $date;

    public function mount($date)
    {
        $this->date = $date;
    }

    public function table(Table $table): Table
    {
        $parsedDate = Carbon::parse($this->date);

        return $table
            ->query(
                Leave::query()
                    ->with('employee.personalData')
                    ->where('status', 'approved')
                    ->whereDate('start_date', '<=', $parsedDate)
                    ->whereDate('end_date', '>=', $parsedDate)
            )
            ->columns([
                TextColumn::make('employee_id')
                    ->label(__('ID'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('employee.full_name')
                    ->label(__('Employee'))
                    ->formatStateUsing(fn ($record) => \Illuminate\Support\Str::title($record->employee->full_name))
                    ->searchable()
                    ->sortable(),

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

                TextColumn::make('reason')
                    ->label(__('Reason'))
                    ->wrap()
                    ->placeholder('-'),
            ])
            ->paginated(false)
            ->emptyStateHeading(__('No employees are on leave this day.'));
    }

    public function render()
    {
        return view('livewire.hr.leave-day-table');
    }
}
