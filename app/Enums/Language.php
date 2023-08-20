<?php

namespace App\Enums;

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
            self::ENGLISH         => __("languages.english"),
            self::ARABIC          => __("languages.arabic"),
            self::CHINESE         => __("languages.chinese"),
            self::FRENCH          => __("languages.french"),
            self::GERMAN          => __("languages.german"),
            self::GREEK           => __("languages.greek"),
            self::ITALIAN         => __("languages.italian"),
            self::JAPANESE        => __("languages.japanese"),
            self::KOREAN          => __("languages.korean"),
            self::POLNISH         => __("languages.polnish"),
            self::PORTUGUESE      => __("languages.portuguese"),
            self::RUSSIAN         => __("languages.russian"),
            self::SPANISH         => __("languages.spanish"),
            self::TURKISH         => __("languages.turkish"),
        };
    }

    public static function getLabels(): array
    {
        return collect(self::cases())->map(fn ($language) => ['value' => $language->value, 'name' => $language->label()])->toArray();
    }

    public static function getKeyValues(): array
    {
        return collect(self::cases())->flatMap(fn ($language) => [$language->value => $language->label()])->toArray();
    }

    public static function voiceEnabled(): array
    {
        return collect(self::cases())->filter(fn ($language) => in_array($language->value, [
            'en',
            'pt',
            'es'
        ]))->toArray();
    }
}
