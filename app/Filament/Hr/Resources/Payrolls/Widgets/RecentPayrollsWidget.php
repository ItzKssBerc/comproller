<?php

namespace App\Filament\Hr\Resources\Payrolls\Widgets;

use App\Models\Payroll;
use Filament\Actions\Action;
use Filament\Support\Enums\Size;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentPayrollsWidget extends BaseWidget
{
    use \App\Traits\HasPayrollGeneration;

    protected static ?string $heading = 'Recent Completed Payrolls';

    protected int|string|array $columnSpan = 'full';

    public function getHeading(): string
    {
        return __('Completed Payrolls (:month)', ['month' => now()->translatedFormat('F')]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Payroll::query()
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->latest()
            )
            ->columns([
                TextColumn::make('employee_id')
                    ->label(__('ID'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('employee.personalData.last_name')
                    ->label(__('Employee'))
                    ->formatStateUsing(fn(Payroll $record) => $record->employee->personalData?->last_name . ' ' . $record->employee->personalData?->first_name)
                    ->searchable(['last_name', 'first_name']),
                TextColumn::make('period')
                    ->label(__('Period'))
                    ->color('gray'),
                TextColumn::make('gross_amount')
                    ->label(__('Gross'))
                    ->money('HUF')
                    ->color('success')
                    ->weight('bold'),
                TextColumn::make('is_forwarded')
                    ->label(__('Forwarded'))
                    ->badge()
                    ->state(fn(Payroll $record) => $record->is_forwarded ? __('Yes') : __('No'))
                    ->color(fn(Payroll $record) => $record->is_forwarded ? 'success' : 'gray')
                    ->icon(fn(Payroll $record) => $record->is_forwarded ? 'heroicon-m-check-circle' : 'heroicon-m-x-circle'),
                TextColumn::make('created_at')
                    ->label(__('Processed At'))
                    ->dateTime('Y. m. d. H:i')
                    ->color('gray')
                    ->size('xs'),
            ])
            ->actions([
                Action::make('recalculate')
                    ->label(__('Recalculate'))
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->size(Size::Small)
                    ->requiresConfirmation()
                    ->modalHeading(__('Recalculate Payroll?'))
                    ->modalDescription(__('This will refresh all amounts based on current rates and linked data.'))
                    ->action(fn(Payroll $record) => $this->recalculatePayroll($record)),
                Action::make('mark_forwarded')
                    ->label(__('Mark Forwarded'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->size(Size::Small)
                    ->requiresConfirmation()
                    ->modalHeading(__('Mark as Forwarded?'))
                    ->modalDescription(__('This payroll will be moved to Finance and removed from this list.'))
                    ->hidden(fn(Payroll $record) => $record->is_forwarded)
                    ->action(fn(Payroll $record) => $record->update(['is_forwarded' => true])),
                Action::make('download')
                    ->label(__('Download'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('primary')
                    ->size(Size::Small)
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
            ->bulkActions([
                \Filament\Actions\BulkAction::make('bulk_recalculate')
                    ->label(__('Recalculate'))
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(fn(\Illuminate\Database\Eloquent\Collection $records) => $records->each(fn($record) => $this->recalculatePayroll($record))),

                \Filament\Actions\BulkAction::make('bulk_generate_payslips')
                    ->label(__('Generate Payslips'))
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
                    ->action(function (\Illuminate\Database\Eloquent\Collection $records, array $data) {
                        return redirect()->to(route('payrolls.bulk-download', [
                            'ids' => $records->pluck('id')->toArray(),
                            'locale' => $data['locale'],
                            'dual_copy' => $data['dual_copy'],
                        ]));
                    }),
            ])
            ->emptyStateHeading(__('No completed payrolls for this month yet.'));
    }
}
