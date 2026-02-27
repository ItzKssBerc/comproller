<?php

namespace App\Filament\Hr\Resources\Payrolls\Tables;

use App\Models\Payroll;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PayrollsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee_id')
                    ->label(__('ID'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('employee.personalData.last_name')
                    ->label(__('Last Name'))
                    ->searchable(),
                TextColumn::make('employee.personalData.first_name')
                    ->label(__('First Name'))
                    ->searchable(),
                TextColumn::make('period')
                    ->label(__('Period'))
                    ->searchable(),
                TextColumn::make('gross_amount')
                    ->label(__('Gross Amount'))
                    ->money('HUF')
                    ->sortable(),
                TextColumn::make('is_forwarded')
                    ->label(__('Forwarded'))
                    ->badge()
                    ->state(fn (Payroll $record) => $record->is_forwarded ? __('Yes') : __('No'))
                    ->color(fn (Payroll $record) => $record->is_forwarded ? 'success' : 'gray')
                    ->icon(fn (Payroll $record) => $record->is_forwarded ? 'heroicon-m-check-circle' : 'heroicon-m-x-circle'),
                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'completed' => 'success',
                        default => 'gray',
                    })
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                \Filament\Tables\Filters\TernaryFilter::make('is_forwarded')
                    ->label(__('Forwarded'))
                    ->placeholder(__('All Records'))
                    ->trueLabel(__('Forwarded Only'))
                    ->falseLabel(__('Not Forwarded Only')),
            ])
            ->recordActions([
                Action::make('recalculate')
                    ->label(__('Recalculate'))
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading(__('Recalculate Payroll?'))
                    ->modalDescription(__('This will refresh all amounts based on current rates and linked data.'))
                    ->action(fn (Payroll $record, $livewire) => $livewire->recalculatePayroll($record)),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('bulk_download')
                        ->label(__('Bulk Download'))
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $ids = $records->pluck('id')->toArray();

                            return redirect()->route('payrolls.bulk-download', [
                                'ids' => $ids,
                                'locale' => app()->getLocale(),
                                'dual_copy' => false, // Default to false for bulk
                            ]);
                        }),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
