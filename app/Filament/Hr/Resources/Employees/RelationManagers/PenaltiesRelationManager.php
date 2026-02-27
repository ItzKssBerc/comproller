<?php

namespace App\Filament\Hr\Resources\Employees\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PenaltiesRelationManager extends RelationManager
{
    protected static string $relationship = 'penalties';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('type')
                    ->label(__('Type'))
                    ->options([
                        'lost_key' => __('Lost Key'),
                        'lost_rfid' => __('Lost RFID Card'),
                        'lost_qr' => __('Lost QR Card'),
                        'other' => __('Other Reason'),
                    ])
                    ->required()
                    ->native(false),
                TextInput::make('amount')
                    ->label(__('Amount'))
                    ->numeric()
                    ->required()
                    ->suffix('HUF'),
                Textarea::make('description')
                    ->label(__('Description'))
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Toggle::make('is_processed')
                    ->label(__('Processed'))
                    ->disabled()
                    ->dehydrated(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('type')
            ->columns([
                TextColumn::make('type')
                    ->label(__('Type'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'lost_key' => 'danger',
                        'lost_rfid' => 'warning',
                        'lost_qr' => 'info',
                        'other' => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => __($state === 'lost_key' ? 'Lost Key' : ($state === 'lost_rfid' ? 'Lost RFID Card' : ($state === 'lost_qr' ? 'Lost QR Card' : 'Other Reason')))),
                TextColumn::make('amount')
                    ->label(__('Amount'))
                    ->money('HUF')
                    ->sortable(),
                IconColumn::make('is_processed')
                    ->label(__('Processed'))
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label(__('Created at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('processed')
                    ->label(__('Previous Penalties'))
                    ->query(fn (Builder $query): Builder => $query->where('is_processed', true))
                    ->toggle(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('Add Penalty')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->label(__('Add Penalty')),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
