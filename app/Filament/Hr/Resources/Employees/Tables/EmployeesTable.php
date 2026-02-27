<?php

namespace App\Filament\Hr\Resources\Employees\Tables;

use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

class EmployeesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(__('ID'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('personalData.last_name')
                    ->label(__('Last Name'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('personalData.first_name')
                    ->label(__('First Name'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('contactData.email')
                    ->label(__('Email address'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('contactData.phone')
                    ->label(__('Phone number'))
                    ->searchable(),
                IconColumn::make('is_active')
                    ->label(__('Active'))
                    ->boolean(),
                TextColumn::make('locker_key')
                    ->label(__('Locker Key'))
                    ->formatStateUsing(fn ($state) => is_array($state) ? implode(', ', $state) : $state)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label(__('Created at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('Updated at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultPaginationPageOption(5)
            ->paginated([5, 10, 25, 50])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('printIdCards')
                        ->label(__('Print ID Cards'))
                        ->icon('heroicon-o-printer')
                        ->color('info')
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records) {
                            $invalidEmployees = $records->filter(
                                fn ($record) => $record->identification?->identification_device !== 'qr_code'
                            );

                            if ($invalidEmployees->count() > 0) {
                                Notification::make()
                                    ->title(__('Selected employees do not have QR-based ID cards'))
                                    ->danger()
                                    ->send();

                                return;
                            }

                            $ids = $records->pluck('id')->toArray();
                            $url = route('employees.bulk-id-card', ['ids' => $ids]);

                            return redirect()->away($url);
                        }),
                    BulkAction::make('printContracts')
                        ->label(__('Print Contracts'))
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
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records, array $data) {
                            $ids = $records->pluck('id')->toArray();
                            $url = route('employees.bulk-contract', [
                                'ids' => $ids,
                                'lang' => $data['lang'],
                                'dual_copy' => $data['dual_copy'],
                            ]);

                            return redirect()->away($url);
                        }),
                    BulkAction::make('printMedicalReferrals')
                        ->label(__('Print Medical Referrals'))
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
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records, array $data) {
                            $ids = $records->pluck('id')->toArray();
                            $url = route('employees.bulk-medical-referral', [
                                'ids' => $ids,
                                'lang' => $data['lang'],
                                'type' => $data['type'],
                            ]);

                            return redirect()->away($url);
                        }),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
