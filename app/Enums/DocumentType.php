<?php

namespace App\Enums;

enum DocumentType: string
{
    case BLOG_POST = 'blog_post';
    case TEXT_TRANSCRIPTION = 'text_transcription';
    case SOCIAL_MEDIA_POST = 'social_media_post';

    public function label()
    {
        return match ($this) {
            self::BLOG_POST => __('common.blog_post'),
            self::TEXT_TRANSCRIPTION => __('common.text_transcription'),
            self::SOCIAL_MEDIA_POST => __('common.social_media_post')
        };
    }

    public static function routeNames()
    {
        return [
            self::BLOG_POST->value => 'blog-post-view',
            self::TEXT_TRANSCRIPTION->value => 'transcription-view',
            self::SOCIAL_MEDIA_POST->value => 'social-media-post-view'
        ];
    }

    public static function getKeyValues(): array
    {
        return collect(self::cases())->flatMap(fn ($type) => [$type->value => $type->label()])->toArray();
    }
}
