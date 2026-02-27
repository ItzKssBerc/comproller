<?php

namespace App\Livewire\Hr;

use App\Models\Employee;
use App\Models\EmployeePenalty as Penalty;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;

class EmployeePenaltyHistory extends Component implements HasActions, HasForms, HasTable
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
            ->query(Penalty::query()->where('employee_id', $this->employee->id))
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('type')
                    ->label(__('Type'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'lost_key' => __('Lost Key'),
                        'lost_rfid' => __('Lost RFID Card'),
                        'lost_qr' => __('Lost QR Card'),
                        default => __('Other Reason'),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'lost_key', 'lost_rfid', 'lost_qr' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('amount')
                    ->label(__('Amount'))
                    ->money('HUF'),
                TextColumn::make('created_at')
                    ->label(__('Date'))
                    ->date('Y. m. d. H:i'),
            ])
            ->headerActions([
                Action::make('add_penalty')
                    ->label(__('Add Penalty'))
                    ->icon('heroicon-o-plus')
                    ->form([
                        Select::make('type')
                            ->label(__('Type'))
                            ->options([
                                'lost_key' => __('Lost Key'),
                                'lost_rfid' => __('Lost RFID Card'),
                                'lost_qr' => __('Lost QR Card'),
                                'other' => __('Other Reason'),
                            ])
                            ->required()
                            ->native(false),
                        TextInput::make('amount')
                            ->label(__('Amount'))
                            ->numeric()
                            ->required()
                            ->suffix('HUF'),
                        Textarea::make('description')
                            ->label(__('Description'))
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])
                    ->action(function (array $data): void {
                        Penalty::create([
                            'employee_id' => $this->employee->id,
                            'type' => $data['type'],
                            'amount' => $data['amount'],
                            'description' => $data['description'] ?? null,
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->title(__('Penalty added successfully.'))
                            ->success()
                            ->send();
                    }),
            ])
            ->paginated(false);
    }

    public function render()
    {
        return view('livewire.hr.employee-penalty-history');
    }
}
