<?php

namespace App\Enums;

enum SourceProvider: string
{
    case YOUTUBE = 'youtube';
    case WEBSITE_URL = 'website_url';
    case FREE_TEXT = 'free_text';

    public function label(): string
    {
        return match ($this) {
            self::YOUTUBE         => __("common.youtube"),
            self::FREE_TEXT       => __("common.free_text"),
            self::WEBSITE_URL     => __("common.website_url"),
        };
    }

    public static function getKeyValues(): array
    {
        return collect(self::cases())->flatMap(fn ($provider) => [$provider->value => $provider->label()])->toArray();
    }
}
