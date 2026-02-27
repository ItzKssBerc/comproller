<?php

namespace App\Filament\Hr\Resources\Employees\Pages;

use App\Filament\Hr\Resources\Employees\EmployeeResource;
use App\Traits\HasPayrollGeneration;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewEmployee extends ViewRecord
{
    use HasPayrollGeneration;

    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('payrolls')
                ->label(__('Payrolls'))
                ->icon('heroicon-o-banknotes')
                ->color('success')
                ->slideOver()
                ->modalHeading(__('Payroll History'))
                ->modalSubmitAction(false)
                ->modalCancelAction(false)
                ->modalContent(fn () => view('livewire.hr.slideover-payroll-history', ['employee' => $this->record])),
            Actions\Action::make('addPenalty')
                ->label(__('Penalties'))
                ->icon('heroicon-o-exclamation-circle')
                ->color('danger')
                ->slideOver()
                ->modalHeading(__('Manage Penalties'))
                ->modalSubmitAction(false)
                ->modalCancelAction(false)
                ->modalContent(fn () => view('livewire.hr.slideover-penalty-history', ['employee' => $this->record])),
            Actions\Action::make('addLeave')
                ->label(__('Leaves'))
                ->icon('heroicon-o-calendar-days')
                ->color('warning')
                ->slideOver()
                ->modalHeading(__('Manage Leaves'))
                ->modalSubmitAction(false)
                ->modalCancelAction(false)
                ->modalContent(fn () => view('livewire.hr.slideover-leave-history', ['employee' => $this->record])),
            Actions\EditAction::make(),
            Actions\ActionGroup::make([
                Actions\Action::make('downloadIdCard')
                    ->label(__('Download ID Card'))
                    ->icon('heroicon-o-identification')
                    ->color('info')
                    ->url(fn ($record) => route('employees.id-card', ['employee' => $record, 'lang' => app()->getLocale()]))
                    ->openUrlInNewTab(),
                Actions\Action::make('downloadContract')
                    ->label(__('Download Contract'))
                    ->icon('heroicon-o-document-text')
                    ->color('success')
                    ->form([
                        \Filament\Forms\Components\Select::make('lang')
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
                        return redirect()->to(route('employees.contract', [
                            'employee' => $record,
                            'lang' => $data['lang'],
                            'dual_copy' => $data['dual_copy'],
                        ]));
                    }),
                Actions\Action::make('downloadMedicalReferral')
                    ->label(__('Medical Fitness Referral'))
                    ->icon('heroicon-o-heart')
                    ->color('danger')
                    ->form([
                        \Filament\Forms\Components\Select::make('lang')
                            ->label(__('Language'))
                            ->options([
                                'hu' => 'Magyar',
                                'en' => 'English',
                            ])
                            ->default(app()->getLocale())
                            ->required(),
                        \Filament\Forms\Components\Select::make('type')
                            ->label(__('Examination Type'))
                            ->options([
                                'New Hire' => __('New Hire'),
                                'Periodic' => __('Periodic'),
                                'Extraordinary' => __('Extraordinary'),
                                'Final' => __('Final'),
                            ])
                            ->default('New Hire')
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        return redirect()->to(route('employees.medical-referral', [
                            'employee' => $record,
                            'lang' => $data['lang'],
                            'type' => $data['type'],
                        ]));
                    }),
            ])
                ->label(__('Download Documents'))
                ->icon('heroicon-o-chevron-down')
                ->color('slate')
                ->button(),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            \App\Filament\Hr\Widgets\EmployeeAttendanceChart::class,
        ];
    }
}
