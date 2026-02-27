<?php

namespace App\Filament\Hr\Resources\Leaves\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LeaveForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Leave Details'))
                    ->schema([
                        Select::make('employee_id')
                            ->label(__('Employee'))
                            ->relationship('employee', 'id')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->full_name)
                            ->searchable(['id']) // Names are encrypted, so we search by ID for now or improve this later
                            ->required(),
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
                        ToggleButtons::make('status')
                            ->label(__('Status'))
                            ->options([
                                'pending' => __('Pending'),
                                'approved' => __('Approved'),
                                'rejected' => __('Rejected'),
                            ])
                            ->colors([
                                'pending' => 'warning',
                                'approved' => 'success',
                                'rejected' => 'danger',
                            ])
                            ->icons([
                                'pending' => 'heroicon-o-clock',
                                'approved' => 'heroicon-o-check-circle',
                                'rejected' => 'heroicon-o-x-circle',
                            ])
                            ->default('pending')
                            ->required()
                            ->hiddenOn('create')
                            ->grouped(),
                        Textarea::make('reason')
                            ->label(__('Reason'))
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
