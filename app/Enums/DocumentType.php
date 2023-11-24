<?php

namespace App\Enums;

enum DocumentType: string
{
    case AUDIO_TRANSCRIPTION = 'audio_transcription';
    case BLOG_POST = 'blog_post';
    case INQUIRY = 'inquiry';
    case PARAPHRASED_TEXT = 'paraphrased_text';
    case SOCIAL_MEDIA_GROUP = 'social_media_group';
    case SOCIAL_MEDIA_POST = 'social_media_post';
    case SUMMARIZER = 'summarizer';
    case TEXT_TO_SPEECH = 'text_to_speech';
    case GENERIC = 'generic';

    public function label()
    {
        return match ($this) {
            self::AUDIO_TRANSCRIPTION => __('common.audio_transcription'),
            self::BLOG_POST => __('common.blog_post'),
            self::INQUIRY => __('common.inquiry'),
            self::PARAPHRASED_TEXT => __('common.paraphrased_text'),
            self::SOCIAL_MEDIA_GROUP => __('common.social_media'),
            self::SOCIAL_MEDIA_POST => __('common.social_media_post'),
            self::SUMMARIZER => __('common.summarizer'),
            self::TEXT_TO_SPEECH => __('common.text_to_speech'),
            self::GENERIC => 'generic'
        };
    }

    public static function routeNames()
    {
        return [
            self::AUDIO_TRANSCRIPTION->value => 'transcription-view',
            self::BLOG_POST->value => 'blog-post-view',
            self::INQUIRY->value => 'inquiry-view',
            self::PARAPHRASED_TEXT->value => 'paraphrase-view',
            self::SOCIAL_MEDIA_GROUP->value => 'social-media-view',
            self::SUMMARIZER->value => 'summary-view',
            self::TEXT_TO_SPEECH->value => 'text-to-audio-view',
        ];
    }

    public static function getKeyValues(): array
    {
        return collect([
            self::AUDIO_TRANSCRIPTION,
            self::BLOG_POST,
            self::INQUIRY,
            self::PARAPHRASED_TEXT,
            self::SOCIAL_MEDIA_GROUP,
            self::SUMMARIZER,
            self::TEXT_TO_SPEECH,
        ])->flatMap(fn ($type) => [$type->value => $type->label()])->toArray();
    }
}
