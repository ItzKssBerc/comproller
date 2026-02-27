<?php

namespace App\Filament\App\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static bool $shouldRegisterNavigation = false;

    public function getTitle(): string
    {
        return __('Settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('Settings');
    }

    protected string $view = 'filament.app.pages.settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'name' => auth()->user()->name,
            'email' => auth()->user()->email,
            'locale' => app()->getLocale(),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Profile Customization'))
                    ->description(__('Here you can modify your profile details and language.'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('Name'))
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label(__('Email address'))
                            ->email()
                            ->required()
                            ->unique(table: 'users', ignorable: auth()->user())
                            ->maxLength(255),

                        Select::make('locale')
                            ->label(__('Language'))
                            ->options([
                                'hu' => 'Magyar',
                                'en' => 'English',
                            ])
                            ->required()
                            ->native(false),

                        TextInput::make('password')
                            ->label(__('New Password'))
                            ->password()
                            ->minLength(8)
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->same('passwordConfirmation')
                            ->helperText(__('Leave blank if you do not want to change your password.')),

                        TextInput::make('passwordConfirmation')
                            ->label(__('Confirm New Password'))
                            ->password()
                            ->dehydrated(false),
                    ])
                    ->columns(2)
                    ->footerActions([
                        Action::make('save')
                            ->label(__('Save'))
                            ->submit('save'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $locale = \Illuminate\Support\Arr::pull($data, 'locale');
        $localeChanged = false;
        if ($locale && $locale !== session('locale')) {
            session()->put('locale', $locale);
            app()->setLocale($locale);
            $localeChanged = true;
        }

        auth()->user()->update($data);

        Notification::make()
            ->success()
            ->title(__('Successful save'))
            ->body(__('Settings have been successfully updated.'))
            ->send();

        if ($localeChanged) {
            $this->redirect(static::getUrl(), navigate: true);
        }
    }
}
