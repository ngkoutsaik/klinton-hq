<?php

namespace App\Filament\Resources\Resumes\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ResumeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        //        $component = [
        //            TextEntry::make('created_at')
        //                ->dateTime()
        //                ->placeholder('-'),
        //            TextEntry::make('updated_at')
        //                ->dateTime()
        //                ->placeholder('-'),
        //            TextEntry::make('title'),
        //            TextEntry::make('intro')->html(true),
        //        ];

        return $schema;
    }
}
