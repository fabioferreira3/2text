<?php

namespace App\Enums;

enum SourceProvider: string
{
    case YOUTUBE = 'youtube';
    case FREE_TEXT = 'free_text';

    public function labels(): string
    {
        return match ($this) {
            self::YOUTUBE         => "Youtube",
            self::FREE_TEXT       => "Free Text",
        };
    }
}
