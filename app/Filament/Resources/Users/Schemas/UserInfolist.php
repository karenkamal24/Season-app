<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Personal Information')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                ImageEntry::make('photo_url')
                                    ->label('Profile Picture')

                                    ->size(120)
                                    ->getStateUsing(
                                        fn($record) => $record->photo_url
                                            ? (str_starts_with($record->photo_url, 'http')
                                                ? $record->photo_url
                                                : asset('storage/' . $record->photo_url))
                                            : asset('images/default-avatar.png')
                                    )
                                    ->extraImgAttributes([
                                        'loading' => 'lazy',
                                        'class' => 'object-cover rounded-full shadow-md',
                                    ]),

                                // ->defaultImageUrl(url('/images/default-avatar.png')),
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('name')
                                            ->label('Full Name')
                                            ->weight('bold')
                                            ->size('lg'),

                                        TextEntry::make('nickname')
                                            ->label('Nickname')
                                            ->placeholder('-'),

                                        TextEntry::make('email')
                                            ->label('Email Address')
                                            ->icon('heroicon-m-envelope')
                                            ->copyable(),

                                        TextEntry::make('phone')
                                            ->label('Phone')
                                            ->icon('heroicon-m-phone')
                                            ->placeholder('-')
                                            ->copyable(),

                                        TextEntry::make('birth_date')
                                            ->label('Date of Birth')
                                            ->date()
                                            ->placeholder('-'),

                                        TextEntry::make('gender')
                                            ->label('Gender')
                                            ->badge()
                                            ->color(fn(string $state): string => match ($state) {
                                                'male' => 'info',
                                                'female' => 'pink',
                                                default => 'gray',
                                            })
                                            ->placeholder('-'),
                                    ])
                                    ->columnSpan(2),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('Account Details')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('role')
                                    ->label('User Role')
                                    ->badge()
                                    ->color(fn(string $state): string => match ($state) {
                                        'admin' => 'danger',
                                        'vendor' => 'warning',
                                        'customer' => 'success',
                                        default => 'gray',
                                    }),

                                    TextEntry::make('is_blocked')
                                    ->label('stuts')
                                    ->formatStateUsing(fn($state) => $state ? 'Inactive ' : 'Active ')
                                    ->color(fn($state) => $state ? 'danger' : 'success'),



                                    TextEntry::make('is_vendor')
                                    ->label('Account type ')
                                    ->formatStateUsing(fn($state) => $state ? 'vendor ' : 'user ')
                                    ->color(fn($state) => $state ? 'danger' : 'success'),


                                // IconEntry::make('has_interests')
                                //     ->label('Has Interests')
                                //     ->boolean(),
                            ]),
                    ])
                    ->columnSpanFull(),

                // Section::make('Location & Preferences')
                //     ->schema([
                //         Grid::make(2)
                //             ->schema([
                //                 TextEntry::make('address')
                //                     ->label('Address')
                //                     ->icon('heroicon-m-map-pin')
                //                     ->placeholder('-')
                //                     ->columnSpanFull(),

                //                 TextEntry::make('city')
                //                     ->label('City')
                //                     ->placeholder('-'),

                //                 TextEntry::make('currency')
                //                     ->label('Currency')
                //                     ->badge()
                //                     ->placeholder('-'),

                //                 TextEntry::make('language')
                //                     ->label('Language')
                //                     ->badge()
                //                     ->placeholder('-'),

                //                 TextEntry::make('lat')
                //                     ->label('Latitude')
                //                     ->placeholder('-'),

                //                 TextEntry::make('lng')
                //                     ->label('Longitude')
                //                     ->placeholder('-'),
                //             ]),
                //     ])
                //     ->collapsed()
                // //     ->columnSpanFull(),

                // Section::make('Social Login')
                //     ->schema([
                //         Grid::make(2)
                //             ->schema([
                //                 TextEntry::make('provider')
                //                     ->label('Provider')
                //                     ->badge()
                //                     ->color('info')
                //                     ->placeholder('-'),

                //                 TextEntry::make('provider_id')
                //                     ->label('Provider ID')
                //                     ->copyable()
                //                     ->placeholder('-'),
                //             ]),
                //     ])
                //     ->collapsed()
                //     ->columnSpanFull(),

                Section::make('Statistics')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('request')
                                    ->label('Total Requests')
                                    ->numeric()
                                    ->badge()
                                    ->color('info'),

                                TextEntry::make('coins')
                                    ->label('Coins Balance')
                                    ->numeric()
                                    ->badge()
                                    ->color('warning')
                                    ->icon('heroicon-m-currency-dollar'),

                                TextEntry::make('trips')
                                    ->label('Total Trips')
                                    ->numeric()
                                    ->badge()
                                    ->color('success')
                                    ->icon('heroicon-m-map'),
                            ]),
                    ])
                    ->columnSpanFull(),



                Section::make('Timestamps')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Account Created')
                                    ->dateTime()
                                    ->placeholder('-'),

                                TextEntry::make('updated_at')
                                    ->label('Last Updated')
                                    ->dateTime()
                                    ->since()
                                    ->placeholder('-'),
                            ]),
                    ])
                    ->collapsed()
                    ->columnSpanFull(),
            ]);
    }
}
