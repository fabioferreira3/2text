<?php

namespace App\Enums;

enum DocumentType: string
{
    case BLOG_POST = 'blog_post';

    public function label()
    {
        return match ($this) {
            self::BLOG_POST => "Blog Post"
        };
    }
}
