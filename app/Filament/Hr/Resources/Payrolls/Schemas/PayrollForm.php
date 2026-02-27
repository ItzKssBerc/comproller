<?php

namespace App\Filament\Hr\Resources\Payrolls\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PayrollForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('employee_id')
                    ->relationship('employee', 'id')
                    ->required(),
                TextInput::make('period')
                    ->required(),
                Textarea::make('base_salary')
                    ->columnSpanFull(),
                TextInput::make('attendance_hours')
                    ->label(__('Attendance Hours')),
                TextInput::make('paid_leave_hours')
                    ->label(__('Paid Leave Hours')),
                TextInput::make('sick_leave_hours')
                    ->label(__('Sick Leave Hours')),
                Textarea::make('total_hours')
                    ->columnSpanFull(),
                Textarea::make('gross_amount')
                    ->columnSpanFull(),
                Textarea::make('net_amount')
                    ->columnSpanFull(),
                TextInput::make('status')
                    ->required()
                    ->default('completed'),
            ]);
    }
}
