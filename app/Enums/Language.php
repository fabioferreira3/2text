<?php

namespace App\Enums;

use Illuminate\Support\Str;

enum Language: string
{
    case ENGLISH = 'en';
    case ARABIC = 'ar';
    case CHINESE = 'ch';
    case FRENCH = 'fr';
    case GERMAN = 'de';
    case GREEK = 'el';
    case KOREAN = 'ko';
    case ITALIAN = 'it';
    case JAPANESE = 'ja';
    case POLNISH = 'pl';
    case PORTUGUESE = 'pt';
    case RUSSIAN = 'ru';
    case SPANISH = 'es';
    case TURKISH = 'tr';

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
            self::RUSSIAN         => "Russian",
            self::SPANISH         => "Spanish",
            self::TURKISH         => "Turkish",
        };
    }

    public static function getLabels(): array
    {
        return collect(self::cases())->map(fn ($language) => ['value' => $language->value, 'name' => Str::of($language->name)->lower()->ucfirst()])->toArray();
    }
}
