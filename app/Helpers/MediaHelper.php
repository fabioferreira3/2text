<?php

namespace App\Helpers;

use App\Enums\DocumentType;
use App\Models\Document;

class MediaHelper
{
    public function getSocialMediaImageSize(string $platform)
    {
        $size = null;
        switch ($platform) {
            case 'instagram':
                $size = ['height' => 1024, 'width' => 1024];
                break;
            case 'linkedin':
                $size = ['height' => 1024, 'width' => 1792];
                break;
            case 'facebook':
                $size = ['height' => 1024, 'width' => 1792];
                break;
            case 'twitter':
                $size = ['height' => 1024, 'width' => 1792];
                break;
            default:
                $size = ['height' => 1024, 'width' => 1024];
        }

        return $size;
    }

    public function getImageSizeByDocumentType(Document $document)
    {
        if ($document->type === DocumentType::SOCIAL_MEDIA_POST) {
            return self::getSocialMediaImageSize($document->getMeta('platform'));
        }

        if ($document->type === DocumentType::BLOG_POST) {
            return ['height' => 1024, 'width' => 1792];
        }

        return ['height' => 1024, 'width' => 1024];
    }

    public function convertWebVttToPlainText($webVttContent)
    {
        // Remove the WebVTT file header
        $webVttContent = preg_replace(
            '/^WEBVTT.*\n(?:\d{2}:\d{2}:\d{2}.\d{3} --> \d{2}:\d{2}:\d{2}.\d{3}.*\n)?/m',
            '',
            $webVttContent
        );

        // Remove timestamps and cue settings
        $webVttContent = preg_replace('/\d{2}:\d{2}:\d{2}.\d{3} --> \d{2}:\d{2}:\d{2}.\d{3}.*\n/', '', $webVttContent);

        // Split the content into lines
        $lines = preg_split('/\n+/', $webVttContent);

        $uniqueLines = [];
        $previousLine = '';

        foreach ($lines as $line) {
            // Remove HTML tags and extra whitespace
            $cleanLine = trim(strip_tags($line));

            // Skip empty lines
            if (empty($cleanLine)) {
                continue;
            }

            // Add the line if it's not a duplicate
            if ($cleanLine !== $previousLine) {
                $uniqueLines[] = $cleanLine;
                $previousLine = $cleanLine;
            }
        }

        // Combine the unique lines back into a single string
        return implode(' ', $uniqueLines);
    }
}
