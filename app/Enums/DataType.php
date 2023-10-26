<?php

namespace App\Enums;

enum DataType: string
{
    case CSV = 'csv';
    case DOCX = 'docx';
    case DOCS_SITE = 'docs_site';
    case JSON = 'json';
    case MDX = 'mdx';
    case NOTION = 'notion';
    case PDF = 'pdf_file';
    case SITEMAP = 'sitemap';
    case TEXT = 'text';
    case WEB_PAGE = 'web_page';
    case YOUTUBE = 'youtube';

    public function label()
    {
        return match ($this) {
            self::CSV => 'csv',
            self::DOCX => 'docx',
            self::DOCS_SITE => 'Doc site',
            self::JSON => 'json',
            self::MDX => 'mdx',
            self::NOTION => 'Notion site',
            self::PDF => 'PDF',
            self::SITEMAP => 'Sitemap',
            self::TEXT => 'Free Text',
            self::WEB_PAGE => 'Web page',
            self::YOUTUBE => 'Youtube link',
        };
    }

    public static function getKeyValues(): array
    {
        return collect(self::cases())->flatMap(fn ($type) => [$type->value => $type->label()])->toArray();
    }
}
