<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

/**
 * Case order is the order the skill sections appear on the resume.
 */
enum SkillType: string implements HasLabel
{
    case GENERAL = 'general';
    case LANGUAGE_FRAMEWORK = 'language_framework';
    case TOOL = 'tool';
    case SPOKEN_LANGUAGE = 'spoken_language';

    public function getLabel(): string
    {
        return match ($this) {
            self::GENERAL => 'General',
            self::LANGUAGE_FRAMEWORK => 'Languages & Frameworks',
            self::TOOL => 'Tools',
            self::SPOKEN_LANGUAGE => 'Spoken Languages',
        };
    }
}
