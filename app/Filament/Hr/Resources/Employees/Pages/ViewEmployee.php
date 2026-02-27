<?php

namespace App\Filament\Hr\Resources\Employees\Pages;

use App\Filament\Hr\Resources\Employees\EmployeeResource;
use Filament\Actions;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewEmployee extends ViewRecord
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('downloadIdCard')
                ->label(__('Download ID Card'))
                ->icon('heroicon-o-identification')
                ->color('info')
                ->url(fn($record) => route('employees.id-card', ['employee' => $record, 'lang' => app()->getLocale()]))
                ->openUrlInNewTab(),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            \App\Filament\Hr\Widgets\EmployeeAttendanceChart::class,
        ];
    }
}
