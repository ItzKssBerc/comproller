<?php

namespace App\Filament\Hr\Widgets;

use App\Models\Attendance;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class AttendanceMonitor extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = [
        'md' => 1,
        'lg' => 1,
    ];

    public function getHeading(): string
    {
        return __('Attendance Monitor');
    }

    protected function getTableHeading(): string
    {
        return __('Attendance Monitor');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Attendance::query()->latest()
            )
            ->columns([
                Tables\Columns\TextColumn::make('employee.id')
                    ->label('ID'),
                Tables\Columns\TextColumn::make('employee.personalData.last_name')
                    ->label(__('Last Name'))
                    ->formatStateUsing(fn ($record) => ($record->employee?->personalData?->last_name ?? '') . ' ' . ($record->employee?->personalData?->first_name ?? '')),
                Tables\Columns\TextColumn::make('clock_in_at')
                    ->label(__('Clock In'))
                    ->dateTime('H:i')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('clock_out_at')
                    ->label(__('Clock Out'))
                    ->dateTime('H:i')
                    ->placeholder('-'),
                Tables\Columns\IconColumn::make('status')
                    ->label(__('Status'))
                    ->getStateUsing(fn ($record) => $record->clock_out_at === null)
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-minus-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),
                Tables\Columns\ToggleColumn::make('is_processed')
                    ->label(__('Processed')),
            ])
            ->paginated([5]);
    }
}
