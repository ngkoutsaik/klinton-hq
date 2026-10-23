<?php

namespace App\Filament\Resources\ContactForms;

use App\Filament\Resources\ContactForms\Pages\ListContactForms;
use App\Filament\Resources\ContactForms\Pages\ViewContactForm;
use App\Filament\Resources\ContactForms\Schemas\ContactFormInfolist;
use App\Filament\Resources\ContactForms\Tables\ContactFormsTable;
use App\Models\ContactForm;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ContactFormResource extends Resource
{
    protected static ?string $model = ContactForm::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static ?string $recordTitleAttribute = 'subject';

    public static function infolist(Schema $schema): Schema
    {
        return ContactFormInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContactFormsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContactForms::route('/'),
            'view' => ViewContactForm::route('/{record}'),
        ];
    }
}
