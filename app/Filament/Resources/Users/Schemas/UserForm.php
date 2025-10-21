<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Select;

use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Personal Information')
                    ->description('Basic user information.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Full Name')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('nickname')
                                    ->label('Nickname')
                                    ->maxLength(255),

                                TextInput::make('email')
                                    ->label('Email Address')
                                    ->email()
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),

                                TextInput::make('phone')
                                    ->label('Phone Number')
                                    ->tel()
                                    ->maxLength(20),

                                DatePicker::make('birth_date')
                                    ->label('Date of Birth')
                                    ->native(false)
                                    ->maxDate(now()),

                                Select::make('gender')
                                    ->label('Gender')
                                    ->options([
                                        'male' => 'Male',
                                        'female' => 'Female',
                                    ])
                                    ->native(false),

                                FileUpload::make('photo_url')
                                    ->label('Profile Picture')
                                    ->image()
                                    ->directory('avatars')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('Location & Settings')
                    ->description('User location and preferences.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('address')
                                    ->label('Address')
                                    ->maxLength(255)
                                    ->columnSpanFull(),

                                TextInput::make('city')
                                    ->label('City')
                                    ->maxLength(100),

                                TextInput::make('currency')
                                    ->label('Currency')
                                    ->maxLength(10)
                                    ->placeholder('e.g., EGP, USD'),

                                TextInput::make('language')
                                    ->label('Preferred Language')
                                    ->maxLength(10)
                                    ->placeholder('e.g., ar, en'),

                                TextInput::make('lat')
                                    ->label('Latitude')
                                    ->numeric()
                                    ->placeholder('e.g., 30.0444'),

                                TextInput::make('lng')
                                    ->label('Longitude')
                                    ->numeric()
                                    ->placeholder('e.g., 31.2357'),
                            ]),
                    ])
                    ->collapsed()
                    ->columnSpanFull(),

                Section::make('Account Settings')
                    ->description('Role, status, and authentication.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('role')
                                    ->label('User Role')
                                    ->options([
                                        'customer' => 'Customer',
                                        'admin' => 'Admin',
                                        'vendor' => 'Vendor',
                                    ])
                                    ->required()
                                    ->default('customer')
                                    ->native(false),

                                TextInput::make('password')
                                    ->label('Password')
                                    ->password()
                                    ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                                    ->dehydrated(fn ($state) => filled($state))
                                    ->required(fn (string $context): bool => $context === 'create')
                                    ->maxLength(255),

                                Toggle::make('is_blocked')
                                    ->label('Block User')
                                    ->inline(false)
                                    ->default(false),

                                Toggle::make('is_vendor')
                                    ->label('Is Vendor')
                                    ->inline(false)
                                    ->default(false),

                                Toggle::make('has_interests')
                                    ->label('Has Interests')
                                    ->inline(false)
                                    ->default(false),

                                DateTimePicker::make('email_verified_at')
                                    ->label('Email Verified At')
                                    ->native(false),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('Social Login')
                    ->description('Third-party authentication details.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('provider')
                                    ->label('Provider')
                                    ->maxLength(50)
                                    ->placeholder('e.g., google, facebook'),

                                TextInput::make('provider_id')
                                    ->label('Provider ID')
                                    ->maxLength(255),
                            ]),
                    ])
                    ->collapsed()
                    ->columnSpanFull(),

                Section::make('Statistics & OTP')
                    ->description('User activity and verification.')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('request')
                                    ->label('Requests')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0),

                                TextInput::make('coins')
                                    ->label('Coins')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0),

                                TextInput::make('trips')
                                    ->label('Trips')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0),

                                TextInput::make('last_otp')
                                    ->label('Last OTP')
                                    ->maxLength(10),

                                DateTimePicker::make('last_otp_expire')
                                    ->label('OTP Expiration')
                                    ->native(false),
                            ]),
                    ])
                    ->collapsed()
                    ->columnSpanFull(),
            ]);
    }
}
