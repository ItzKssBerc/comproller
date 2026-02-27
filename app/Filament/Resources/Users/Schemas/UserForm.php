<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\UserRole;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                DateTimePicker::make('email_verified_at'),
                TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn($state) => \Illuminate\Support\Facades\Hash::make($state))
                    ->dehydrated(fn($state) => filled($state))
                    ->required(fn(string $operation): bool => $operation === 'create'),
                Select::make('role')
                    ->options(function () {
                        $user = auth()->user();
                        if ($user->role === UserRole::App) {
                            return [
                                UserRole::Admin->value => UserRole::Admin->getLabel(),
                                UserRole::HR->value => UserRole::HR->getLabel(),
                                UserRole::Finance->value => UserRole::Finance->getLabel(),
                                UserRole::Camera->value => UserRole::Camera->getLabel(),
                            ];
                        }
                        if ($user->role === UserRole::Admin) {
                            return [
                                UserRole::HR->value => UserRole::HR->getLabel(),
                                UserRole::Finance->value => UserRole::Finance->getLabel(),
                                UserRole::Camera->value => UserRole::Camera->getLabel(),
                            ];
                        }
                        return [];
                    })
                    ->required(),
            ]);
    }
}
