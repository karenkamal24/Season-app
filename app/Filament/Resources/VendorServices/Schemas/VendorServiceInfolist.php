<?php

namespace App\Filament\Resources\VendorServices\Schemas;

use Filament\Schemas\Schema;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Schemas\Components\Section;

class VendorServiceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Details')
                ->description('Main vendor service details')
                ->inlineLabel()
                ->components([
                    TextEntry::make('user.name')
                        ->label('Vendor')
                        ->placeholder('-'),

                    TextEntry::make('user.email')
                        ->label('Vendor Email')
                        ->placeholder('-'),

                    TextEntry::make('user.phone')
                        ->label('Vendor Phone')
                        ->placeholder('-'),

                    TextEntry::make('serviceType.name_en')
                        ->label('Service Type')
                        ->placeholder('-'),

                    TextEntry::make('name')
                        ->label('Service Name'),

                    TextEntry::make('description')
                        ->label('Description')
                        ->placeholder('-'),
                ]),

            Section::make('Contact & Location')
                ->icon('heroicon-o-map-pin')
                ->inlineLabel()
                ->components([
                    TextEntry::make('contact_number')
                        ->label('Contact Number')
                        ->placeholder('-'),

                    TextEntry::make('address')
                        ->label('Address')
                        ->placeholder('-'),

                    TextEntry::make('latitude')
                        ->label('Latitude')
                        ->placeholder('-'),

                    TextEntry::make('longitude')
                        ->label('Longitude')
                        ->placeholder('-'),
                ]),

            Section::make('Files & Status')
                ->icon('heroicon-o-document')
                ->inlineLabel()
                ->components([
                    TextEntry::make('commercial_register')
                        ->label('Commercial Register')
                        ->url(fn($record) => $record->commercial_register ? asset('storage/' . $record->commercial_register) : null)
                        ->openUrlInNewTab()
                        ->placeholder('-'),

                    ImageEntry::make('images')
                        ->label('Service Images')
                        ->hiddenLabel()
                        ->limit(10),

                    TextEntry::make('status')
                        ->label('Status')
                        ->badge(),

                    TextEntry::make('created_at')
                        ->label('Created At')
                        ->dateTime(),

                    TextEntry::make('updated_at')
                        ->label('Updated At')
                        ->dateTime(),
                ]),
        ]);
    }
}
