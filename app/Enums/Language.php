<?php

namespace App\Enums;

enum Language: string
{
    case ENGLISH = 'en';
    case PORTUGUESE = 'pt';
    case SPANISH = 'es';
    case JAPANESE = 'ja';
    case CHINESE = 'ch';
    case KOREAN = 'ko';
    case GERMAN = 'de';
    case FRENCH = 'fr';
    case ITALIAN = 'it';
    case POLNISH = 'pl';
    case TURKISH = 'tr';
    case ARABIC = 'ar';
    case GREEK = 'el';

    public function label(): string
    {
        return match ($this) {
            self::ENGLISH         => "English",
            self::ARABIC          => "Arabic",
            self::CHINESE         => "Chinese",
            self::FRENCH          => "French",
            self::GERMAN          => "German",
            self::GREEK           => "Greek",
            self::ITALIAN         => "Italian",
            self::JAPANESE        => "Japanese",
            self::KOREAN          => "Korean",
            self::POLNISH         => "Polnish",
            self::PORTUGUESE      => "Portuguese",
            self::SPANISH         => "Spanish",
            self::TURKISH         => "Turkish",
        };
    }
}
