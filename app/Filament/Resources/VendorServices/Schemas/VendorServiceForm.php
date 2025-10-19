<?php

namespace App\Filament\Resources\VendorServices\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;


class VendorServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Vendor Info')
                ->description('Basic information about the vendor service')
                ->schema([
                    Select::make('user_id')
                        ->label('Vendor')
                        ->relationship('user', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('service_type_id')
                        ->label('Service Type')
                        ->relationship('serviceType', 'name_en')
                        ->searchable()
                        ->preload()
                        ->required(),

                    TextInput::make('name')
                        ->label('Service Name')
                        ->required()
                        ->maxLength(255),

                    Textarea::make('description')
                        ->label('Description')
                        ->placeholder('Write short description about the service...')
                        ->rows(3)
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make('Contact & Location')
                ->icon('heroicon-o-map-pin')
                ->schema([
                    TextInput::make('contact_number')
                        ->label('Contact Number')
                        ->tel(),

                    TextInput::make('address')
                        ->label('Address'),

                    TextInput::make('latitude')
                        ->numeric()
                        ->step('any')
                        ->label('Latitude'),

                    TextInput::make('longitude')
                        ->numeric()
                        ->step('any')
                        ->label('Longitude'),
                ])
                ->columns(2),

            Section::make('Files & Status')
                ->icon('heroicon-o-document')
                ->schema([
                    FileUpload::make('commercial_register')
                        ->label('Commercial Register')
                        ->directory('vendor_services/registers')
                        ->preserveFilenames()
                        ->downloadable()
                        ->openable(),

                    FileUpload::make('images')
                        ->label('Service Images')
                        ->directory('vendor_services/images')
                        ->multiple()
                        ->reorderable()
                        ->image()
                        ->downloadable()
                        ->imageEditor(),

                    Select::make('status')
                        ->label('Status')
                        ->options([
                            'pending' => 'Pending',
                            'approved' => 'Approved',
                            'rejected' => 'Rejected',
                            'disabled' => 'Disabled',
                        ])
                        ->default('pending')
                        ->native(false)
                        ->required(),
                ])
                ->columns(2),
        ]);
    }
}
