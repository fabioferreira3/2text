<?php

namespace App\Enums;

enum SourceProvider: string
{
    case CSV = 'csv';
    case DOCX = 'docx';
    case FREE_TEXT = 'free_text';
        //  case JSON = 'json';
    case PDF = 'pdf_file';
    case YOUTUBE = 'youtube';
    case WEBSITE_URL = 'website_url';

    public function label(): string
    {
        return match ($this) {
            self::CSV     => __("common.csv_file"),
            //  self::JSON     => __("common.json_file"),
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
