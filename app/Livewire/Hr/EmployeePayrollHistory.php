<?php

namespace App\Livewire\Hr;

use App\Models\Employee;
use App\Models\Payroll;
use App\Traits\HasPayrollGeneration;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;

class EmployeePayrollHistory extends Component implements HasActions, HasForms, HasTable
{
    use HasPayrollGeneration;
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
            ->query(Payroll::query()->where('employee_id', $this->employee->id))
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('period')
                    ->label(__('Month'))
                    ->weight('bold')
                    ->searchable(),
                TextColumn::make('gross_amount')
                    ->label(__('Gross Amount'))
                    ->money('HUF')
                    ->color('success'),
                TextColumn::make('net_amount')
                    ->label(__('Net Amount'))
                    ->money('HUF')
                    ->color('primary')
                    ->weight('bold'),
                TextColumn::make('created_at')
                    ->label(__('Date'))
                    ->date('Y. m. d.'),
            ])
            ->headerActions([
                $this->getGeneratePayrollAction()
                    ->label(__('Generate'))
                    ->icon('heroicon-o-plus')
                    ->hidden(fn () => $this->employee->payrolls()->where('period', 'like', '%'.now()->format('Y. m.').'%')->exists()),
            ])
            ->actions([
                Action::make('download')
                    ->label(__('Download'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('primary')
                    ->form([
                        \Filament\Forms\Components\Select::make('locale')
                            ->label(__('Language'))
                            ->options([
                                'hu' => 'Magyar',
                                'en' => 'English',
                            ])
                            ->default(app()->getLocale())
                            ->required(),
                        \Filament\Forms\Components\Checkbox::make('dual_copy')
                            ->label(__('Dual Copy'))
                            ->default(false),
                    ])
                    ->action(function ($record, array $data) {
                        return redirect()->to(route('payrolls.download', [
                            'payroll' => $record,
                            'locale' => $data['locale'],
                            'dual_copy' => $data['dual_copy'],
                        ]));
                    }),
            ])
            ->paginated(false);
    }

    public function render()
    {
        return view('livewire.hr.employee-payroll-history');
    }
}
