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
                                fn($record) => $record->identification?->identification_device !== 'qr_code'
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
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
