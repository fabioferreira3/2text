<?php

namespace App\Enums;

enum SourceProvider: string
{
    case YOUTUBE = 'youtube';
    case WEBSITE_URL = 'website_url';
    case FREE_TEXT = 'free_text';
    case DOCX = 'docx';
    case PDF = 'pdf';

    public function label(): string
    {
        return match ($this) {
            self::FREE_TEXT       => __("common.free_text"),
            self::WEBSITE_URL     => __("common.website_url"),
            self::YOUTUBE         => __("common.youtube"),
            self::DOCX     => __("common.docx_file"),
            self::PDF     => __("common.pdf_file"),
        };
    }

    public static function getKeyValues(): array
    {
        return collect(self::cases())->flatMap(fn ($provider) => [$provider->value => $provider->label()])->toArray();
    }
}
