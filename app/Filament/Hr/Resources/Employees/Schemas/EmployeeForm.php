<?php

namespace App\Filament\Hr\Resources\Employees\Schemas;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class EmployeeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Placeholder: Identification removed from here
                Group::make()
                    ->schema([
                        Section::make(__('Personal Data'))
                            ->relationship('personalData')
                            ->schema([
                                TextInput::make('last_name')
                                    ->label(__('Last Name'))
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($set, $get) => self::updateQrCodeHash($set, $get)),
                                TextInput::make('first_name')
                                    ->label(__('First Name'))
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($set, $get) => self::updateQrCodeHash($set, $get)),
                                DatePicker::make('date_of_birth')
                                    ->label(__('Date of Birth')),
                                TextInput::make('mothers_name')
                                    ->label(__('Mother\'s maiden name'))
                                    ->maxLength(255),
                            ])
                            ->columns(2),

                        Section::make(__('Contact Information'))
                            ->relationship('contactData')
                            ->schema([
                                TextInput::make('email')
                                    ->label(__('Email address'))
                                    ->email()
                                    ->maxLength(255),
                                TextInput::make('phone')
                                    ->label(__('Phone number'))
                                    ->tel()
                                    ->maxLength(255),
                                TextInput::make('address')
                                    ->label(__('Address'))
                                    ->columnSpanFull()
                                    ->maxLength(255),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('Financial Data'))
                            ->relationship('financialData')
                            ->schema([
                                TextInput::make('tax_number')
                                    ->label(__('Tax Number'))
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($set, $get) => self::updateQrCodeHash($set, $get)),
                                TextInput::make('social_security_number')
                                    ->label(__('Social Security Number'))
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($set, $get) => self::updateQrCodeHash($set, $get)),
                                TextInput::make('bank_account_number')
                                    ->label(__('Bank Account Number')),
                            ])
                            ->columns(1),

                        Section::make(__('Salary Detail'))
                            ->relationship('salaryDetail')
                            ->schema([
                                TextInput::make('base_hourly_rate')
                                    ->label(__('Base Hourly Rate'))
                                    ->numeric(),
                            ])
                            ->columns(1),
                    ])
                    ->columnSpan(['lg' => 1]),

                Section::make(__('Identification'))
                    ->relationship('identification')
                    ->schema([
                        TextInput::make('citizenship')
                            ->label(__('Citizenship'))
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($set, $get) => self::updateQrCodeHash($set, $get)),
                        TextInput::make('id_card_number')
                            ->label(__('ID Card Number'))
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($set, $get) => self::updateQrCodeHash($set, $get)),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make(__('Device Configuration'))
                    ->relationship('identification')
                    ->schema([
                        Select::make('identification_device')
                            ->label(__('Identification Device'))
                            ->options([
                                'qr_code' => __('QR Code Card'),
                                'rfid' => __('RFID Card'),
                                'nfc' => __('NFC'),
                                'id_card' => __('ID Card'),
                            ])
                            ->nullable()
                            ->native(false)
                            ->columnSpanFull()
                            ->live()
                            ->afterStateUpdated(fn ($set, $get) => self::updateQrCodeHash($set, $get)),

                        TextInput::make('device_identifier')
                            ->label(__('Device Identifier'))
                            ->visible(fn ($get) => in_array($get('identification_device'), ['rfid', 'nfc', 'id_card']))
                            ->required(fn ($get) => in_array($get('identification_device'), ['rfid', 'nfc', 'id_card']))
                            ->maxLength(255)
                            ->columnSpanFull(),

                        TextInput::make('qr_code_hash')
                            ->label(__('QR Code Hash'))
                            ->hidden()
                            ->dehydrated()
                            ->columnSpanFull(),

                        Placeholder::make('qr_code_preview')
                            ->label(__('QR Code Preview'))
                            ->content(fn ($get) => self::generateQrCodeSvg($get('qr_code_hash')))
                            ->visible(fn ($get) => $get('identification_device') === 'qr_code' && $get('qr_code_hash')),
                    ])
                    ->columns(1)
                    ->columnSpanFull(),

                Section::make(__('Access and Equipment'))
                    ->schema([
                        Repeater::make('locker_key')
                            ->label(__('Locker Key'))
                            ->simple(TextInput::make('key')->label(__('Key Number')))
                            ->addActionLabel(__('Add Locker Key'))
                            ->reorderable(false)
                            ->collapsed(false)
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->columnSpanFull(),

                Section::make(__('Status'))
                    ->relationship('contract')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                ToggleButtons::make('employment_type')
                                    ->label(__('Employment Type'))
                                    ->options([
                                        'seasonal' => __('Seasonal Employee'),
                                        'permanent' => __('Permanent Employee'),
                                    ])
                                    ->icons([
                                        'seasonal' => 'heroicon-o-sun',
                                        'permanent' => 'heroicon-o-home',
                                    ])
                                    ->colors([
                                        'seasonal' => 'warning',
                                        'permanent' => 'success',
                                    ])
                                    ->default('permanent')
                                    ->required()
                                    ->grouped(),

                                ToggleButtons::make('employment_term')
                                    ->label(__('Employment Term'))
                                    ->options([
                                        'indefinite' => __('Indefinite'),
                                        'fixed_term' => __('Fixed-term'),
                                    ])
                                    ->icons([
                                        'indefinite' => 'heroicon-o-arrow-path',
                                        'fixed_term' => 'heroicon-o-calendar-days',
                                    ])
                                    ->colors([
                                        'indefinite' => 'info',
                                        'fixed_term' => 'warning',
                                    ])
                                    ->default('indefinite')
                                    ->required()
                                    ->live()
                                    ->grouped(),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('position')
                                    ->label(__('Position'))
                                    ->maxLength(255),
                                TextInput::make('shift')
                                    ->label(__('Shift'))
                                    ->maxLength(255),
                            ]),

                        Grid::make(1)
                            ->schema([
                                Group::make()
                                    ->schema([
                                        Toggle::make('has_scheduled_activation')
                                            ->label(__('Scheduled Status Active'))
                                            ->live()
                                            ->dehydrated(false)
                                            ->afterStateHydrated(fn ($state, $record, $set) => $set('has_scheduled_activation', $record?->scheduled_activation_at !== null)),
                                        DateTimePicker::make('scheduled_activation_at')
                                            ->label(__('Activation Date'))
                                            ->visible(fn ($get) => $get('has_scheduled_activation'))
                                            ->required(fn ($get) => $get('has_scheduled_activation')),
                                    ]),
                            ]),

                        Grid::make(1)
                            ->schema([
                                DateTimePicker::make('scheduled_deactivation_at')
                                    ->label(__('Deactivation Date'))
                                    ->visible(fn ($get) => $get('employment_term') === 'fixed_term')
                                    ->required(fn ($get) => $get('employment_term') === 'fixed_term'),
                            ]),
                    ])
                    ->columns(1)
                    ->columnSpanFull(),
            ])
            ->columns(3);
    }

    protected static function updateQrCodeHash($set, $get): void
    {
        if ($get('identification.identification_device') !== 'qr_code') {
            return;
        }

        $lastName = $get('personalData.last_name') ?? '';
        $firstName = $get('personalData.first_name') ?? '';
        $taxNumber = $get('financialData.tax_number') ?? '';
        $ssn = $get('financialData.social_security_number') ?? '';
        $idCard = $get('identification.id_card_number') ?? '';
        $citizenship = $get('identification.citizenship') ?? '';

        $hash = sprintf(
            '%s-%s-%s-%s-%s-%s-%s-%s-%s-%s-%s-%s',
            $lastName,
            $firstName,
            $firstName,
            $lastName,
            $taxNumber,
            $ssn,
            $ssn,
            $taxNumber,
            $idCard,
            $citizenship,
            $citizenship,
            $idCard
        );

        $set('identification.qr_code_hash', $hash);
    }

    protected static function generateQrCodeSvg(?string $hash): ?HtmlString
    {
        if (! $hash) {
            return null;
        }

        try {
            $renderer = new ImageRenderer(
                new RendererStyle(200),
                new SvgImageBackEnd
            );
            $writer = new Writer($renderer);
            $svg = $writer->writeString($hash);

            return new HtmlString('<div class="flex justify-center p-4 bg-white rounded-lg">'.$svg.'</div>');
        } catch (\Exception $e) {
            return new HtmlString('<p class="text-danger">'.__('Error generating QR code').'</p>');
        }
    }
}
