<?php

namespace App\Filament\Resources\ContactForms\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ContactFormInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('created_at')
                    ->label('Received at')
                    ->dateTime(),
                TextEntry::make('email')
                    ->label('Email address'),
                TextEntry::make('subject')
                    ->columnSpanFull(),
                TextEntry::make('message')
                    ->columnSpanFull(),
                IconEntry::make('send_copy')
                    ->boolean(),
                IconEntry::make('sent')
                    ->boolean(),
            ]);
    }
}
