<?php

namespace App\Filament\Resources\Resumes\Schemas;

use App\Enums\LinkIcon;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ResumeForm
{
    public static function configure(Schema $schema): Schema
    {
        $workExperiences = self::getConfiguredRepeaterComponent('workExperiences', 1);

        $workExperiences->schema(
            [
                Grid::make(2)->schema([
                    TextInput::make('role_name')->required(),
                    TextInput::make('company_name')->required(),
                    TextInput::make('location')
                        ->required(),
                    DatePicker::make('start_date')->required(),
                    DatePicker::make('end_date'),
                    Checkbox::make('in_progress'),
                ]),
                Grid::make(1)->schema([self::getConfiguredRichEditorComponent('description')]),
            ]
        );

        $links = self::getConfiguredRepeaterComponent('links', 3);
        $links->schema([
            TextInput::make('title'),
            TextInput::make('url')->required(),
            Select::make('icon')->options(LinkIcon::class)->default(LinkIcon::DEFAULT),
            Checkbox::make('open_in_new_tab'),
        ]);

        $extraInfo = self::getConfiguredRepeaterComponent('extraInfo', 3);
        $extraInfo->schema([
            TextInput::make('title')->required(),
            TextInput::make('value')->required(),
            Checkbox::make('is_active'),
        ])
            ->reorderable();
        $currentUser = auth()->user();
        $user = Section::make('Owner')->schema([
            TextInput::make('first_name')->required()->default($currentUser->first_name),
            TextInput::make('last_name')->required()->default($currentUser->last_name),
        ])
            ->columns(2)
            ->relationship('user');

        $resume = [
            Grid::make(2)->schema([
                TextInput::make('title')
                    ->required(),
            ]),
            $user,
            self::getConfiguredRichEditorComponent('intro'),
            Select::make('skills')
                ->relationship('skills', 'name')
                ->multiple()
                ->searchable()
                ->pivotData([
                    'is_active' => true,
                ])
                ->createOptionForm([
                    TextInput::make('name')
                        ->required()
                        ->unique(),
                ]),
            $extraInfo,
            $links,
            $workExperiences,

            Grid::make(3)->schema([
                Checkbox::make('published'),
                Checkbox::make('looking_for_role'),
            ]),
        ];

        return $schema->components($resume)->columns(1);
    }

    protected static function getConfiguredRichEditorComponent(string $label): RichEditor
    {
        return RichEditor::make($label)
            ->required()
            ->toolbarButtons([
                ['bold', 'italic', 'underline', 'strike', 'link'],
                ['h2', 'h3'],
                ['alignStart', 'alignCenter', 'alignEnd'],
                ['blockquote', 'codeBlock', 'bulletList', 'orderedList'],
                ['undo', 'redo'],
            ]);
    }

    protected static function getConfiguredRepeaterComponent(string $label, int $columns = 2): Repeater
    {
        return Repeater::make($label)
            ->relationship($label)
            ->columns($columns)
            ->reorderable(true)
            ->orderColumn('order')
            ->collapsible();
    }
}
