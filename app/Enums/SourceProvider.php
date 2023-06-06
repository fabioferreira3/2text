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
            self::YOUTUBE         => "Youtube",
            self::FREE_TEXT       => "Free Text",
            self::WEBSITE_URL     => "Website URL"
        };
    }
}
