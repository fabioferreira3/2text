<?php

namespace App\Enums;

enum Language: string
{
    case ENGLISH = 'en';
    case PORTUGUESE = 'pt';

    public function labels(): string
    {
        return match ($this) {
            self::ENGLISH         => "English",
            self::PORTUGUESE       => "Portuguese",
        };
    }
}
