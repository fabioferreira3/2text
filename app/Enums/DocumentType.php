<?php

namespace App\Enums;

enum DocumentType: string
{
    case BLOG_POST = 'blog_post';
    case TEXT_TRANSCRIPTION = 'text_transcription';

    public function label()
    {
        return match ($this) {
            self::BLOG_POST => "Blog Post",
            self::TEXT_TRANSCRIPTION => "Text Transcription",
        };
    }
}
