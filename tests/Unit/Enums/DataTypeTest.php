<?php

use App\Enums\DataType;

describe('DataType test', function () {
    it('has correct values for DataType enum', function () {
        expect(DataType::CSV->value)->toBe('csv')
            ->and(DataType::DOCX->value)->toBe('docx')
            ->and(DataType::DOCS_SITE->value)->toBe('docs_site')
            ->and(DataType::MDX->value)->toBe('mdx')
            ->and(DataType::NOTION->value)->toBe('notion')
            ->and(DataType::PDF->value)->toBe('pdf_file')
            ->and(DataType::SITEMAP->value)->toBe('sitemap')
            ->and(DataType::TEXT->value)->toBe('text')
            ->and(DataType::WEB_PAGE->value)->toBe('web_page')
            ->and(DataType::YOUTUBE->value)->toBe('youtube_video');
    });

    it('has correct labels for DataType enum', function () {
        expect(DataType::CSV->label())->toBe('csv')
            ->and(DataType::DOCX->label())->toBe('docx')
            ->and(DataType::DOCS_SITE->label())->toBe('Doc site')
            ->and(DataType::MDX->label())->toBe('mdx')
            ->and(DataType::NOTION->label())->toBe('Notion site')
            ->and(DataType::PDF->label())->toBe('PDF')
            ->and(DataType::SITEMAP->label())->toBe('Sitemap')
            ->and(DataType::TEXT->label())->toBe('Free Text')
            ->and(DataType::WEB_PAGE->label())->toBe('Web page')
            ->and(DataType::YOUTUBE->label())->toBe('Youtube link');
    });

    it('getValues method returns all enum values', function () {
        $expectedValues = [
            'csv', 'docx', 'docs_site', 'mdx', 'notion',
            'pdf_file', 'sitemap', 'text', 'web_page', 'youtube_video',
        ];
        expect(DataType::getValues())->toBe($expectedValues);
    });

    it('getKeyValues method returns all enum key-value pairs', function () {
        $expectedKeyValues = [
            'csv' => 'csv',
            'docx' => 'docx',
            'docs_site' => 'Doc site',
            'mdx' => 'mdx',
            'notion' => 'Notion site',
            'pdf_file' => 'PDF',
            'sitemap' => 'Sitemap',
            'text' => 'Free Text',
            'web_page' => 'Web page',
            'youtube_video' => 'Youtube link',
        ];
        expect(DataType::getKeyValues())->toBe($expectedKeyValues);
    });
})->group('enums');
