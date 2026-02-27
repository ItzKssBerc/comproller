<?php

namespace App\Filament\Hr\Resources\Employees\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EmployeeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Quick Statistics'))
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('age')
                                    ->label(__('Age'))
                                    ->getStateUsing(function ($record) {
                                        $dob = $record->personalData?->date_of_birth;
                                        if (! $dob) {
                                            return '-';
                                        }

                                        return \Carbon\Carbon::parse($dob)->age.' '.__('years');
                                    })
                                    ->icon('heroicon-o-user')
                                    ->color('info'),

                                TextEntry::make('monthly_cost')
                                    ->label(__('Estimated Monthly Cost'))
                                    ->getStateUsing(function ($record) {
                                        $rate = $record->salaryDetail?->base_hourly_rate;
                                        if (! $rate) {
                                            return '-';
                                        }
                                        $cost = floatval($rate) * 160;

                                        return number_format($cost, 0, '.', ' ').' HUF';
                                    })
                                    ->icon('heroicon-o-banknotes')
                                    ->color('success'),

                                TextEntry::make('employment_duration')
                                    ->label(__('Employment Duration'))
                                    ->getStateUsing(function ($record) {
                                        $start = $record->contract?->scheduled_activation_at ?? $record->created_at;
                                        if (! $start) {
                                            return '-';
                                        }

                                        return \Carbon\Carbon::parse($start)->diffForHumans(null, true);
                                    })
                                    ->icon('heroicon-o-clock')
                                    ->color('warning'),

                                TextEntry::make('remaining_leaves')
                                    ->label(__('Remaining Leaves'))
                                    ->getStateUsing(function ($record) {
                                        $dob = $record->personalData?->date_of_birth;
                                        $totalLeaves = 20; // Hungarian base leave

                                        if ($dob) {
                                            $age = \Carbon\Carbon::parse($dob)->age;
                                            $extra = match (true) {
                                                $age >= 45 => 10,
                                                $age >= 43 => 9,
                                                $age >= 41 => 8,
                                                $age >= 39 => 7,
                                                $age >= 37 => 6,
                                                $age >= 35 => 5,
                                                $age >= 33 => 4,
                                                $age >= 31 => 3,
                                                $age >= 28 => 2,
                                                $age >= 25 => 1,
                                                default => 0,
                                            };
                                            $totalLeaves += $extra;
                                        }

                                        $leaves = $record->leaves()
                                            ->where('status', 'approved')
                                            ->whereYear('start_date', now()->year)
                                            ->get();

                                        $usedDays = $leaves->sum(function ($l) {
                                            $d = 0;
                                            $current = $l->start_date->copy();
                                            while ($current->lte($l->end_date)) {
                                                if (! $current->isWeekend()) {
                                                    $d++;
                                                }
                                                $current->addDay();
                                            }

                                            return $d;
                                        });

                                        $remaining = $totalLeaves - $usedDays;

                                        return $remaining.' / '.$totalLeaves.' '.__('days');
                                    })
                                    ->icon('heroicon-o-calendar-days')
                                    ->color('danger'),
                            ]),
                    ])
                    ->compact(),

                Section::make(__('Contract Information'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('contract.position')
                                    ->label(__('Position'))
                                    ->placeholder('-'),
                                TextEntry::make('contract.shift')
                                    ->label(__('Shift'))
                                    ->placeholder('-'),
                            ]),
                    ])
                    ->compact(),

                Section::make(__('System Information'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Group::make([
                                    IconEntry::make('is_active')
                                        ->label(__('Status'))
                                        ->boolean(),
                                ]),
                                Group::make([
                                    TextEntry::make('created_at')
                                        ->label(__('Created at'))
                                        ->dateTime()
                                        ->placeholder('-'),
                                    TextEntry::make('updated_at')
                                        ->label(__('Updated at'))
                                        ->dateTime()
                                        ->placeholder('-'),
                                ]),
                            ]),
                    ])
                    ->collapsible(),
            ]);
    }
}
