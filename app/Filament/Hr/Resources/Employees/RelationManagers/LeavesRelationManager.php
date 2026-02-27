<?php

namespace App\Filament\Hr\Resources\Employees\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LeavesRelationManager extends RelationManager
{
    protected static string $relationship = 'leaves';

    protected static ?string $title = 'Leaves';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('type')
                    ->label(__('Type'))
                    ->options([
                        'paid_leave' => __('Paid leave'),
                        'sick_leave' => __('Sick leave'),
                        'unpaid_leave' => __('Unpaid leave'),
                        'other' => __('Other'),
                    ])
                    ->required()
                    ->native(false),
                \Filament\Forms\Components\ToggleButtons::make('status')
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
                    ->inline()
                    ->required()
                    ->hidden(fn (string $operation): bool => $operation === 'create')
                    ->default('pending'),
                DatePicker::make('start_date')
                    ->label(__('Start Date'))
                    ->required()
                    ->native(false),
                DatePicker::make('end_date')
                    ->label(__('End Date'))
                    ->required()
                    ->native(false)
                    ->afterOrEqual('start_date'),
                Textarea::make('reason')
                    ->label(__('Reason'))
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('type')
            ->defaultSort('start_date', 'desc')
            ->columns([
                TextColumn::make('type')
                    ->label(__('Type'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'paid_leave' => __('Paid leave'),
                        'sick_leave' => __('Sick leave'),
                        'unpaid_leave' => __('Unpaid leave'),
                        default => __('Other'),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'paid_leave' => 'success',
                        'sick_leave' => 'danger',
                        'unpaid_leave' => 'warning',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => __('Pending'),
                        'approved' => __('Approved'),
                        'rejected' => __('Rejected'),
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('start_date')
                    ->label(__('Start Date'))
                    ->date('Y. m. d.')
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label(__('End Date'))
                    ->date('Y. m. d.')
                    ->sortable(),
                TextColumn::make('duration')
                    ->label(__('Duration'))
                    ->state(function ($record) {
                        $start = \Carbon\Carbon::parse($record->start_date);
                        $end = \Carbon\Carbon::parse($record->end_date);

                        return $start->diffInDays($end) + 1 .' '.__('days');
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                \Filament\Actions\Action::make('approve')
                    ->label(__('Approve'))
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (\App\Models\Leave $record) => $record->update(['status' => 'approved']))
                    ->visible(fn (\App\Models\Leave $record) => $record->status === 'pending'),
                \Filament\Actions\Action::make('reject')
                    ->label(__('Reject'))
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (\App\Models\Leave $record) => $record->update(['status' => 'rejected']))
                    ->visible(fn (\App\Models\Leave $record) => $record->status === 'pending'),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
