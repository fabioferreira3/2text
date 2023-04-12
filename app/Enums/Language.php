<?php

namespace App\Enums;

enum Language: string
{
    case ENGLISH = 'en';
    case PORTUGUESE = 'pt';

    public function label(): string
    {
        return match ($this) {
            self::ENGLISH         => "English",
            self::PORTUGUESE       => "Portuguese",
        };
    }
}
