<?php

namespace App\Helpers;

use App\Enums\DocumentType;
use App\Models\Document;
use App\Models\DocumentContentBlock;

class MediaHelper
{
    public static function getSocialMediaImageSize(string $platform)
    {
        $size = null;
        switch ($platform) {
            case 'instagram':
                $size = ['height' => 1024, 'width' => 1024];
                break;
            case 'linkedin':
                $size = ['height' => 768, 'width' => 1344];
                break;
            case 'facebook':
                $size = ['height' => 768, 'width' => 1344];
                break;
            case 'twitter':
                $size = ['height' => 640, 'width' => 1536];
                break;
            default:
                $size = ['height' => 1024, 'width' => 1024];
        }

        return $size;
    }

    public static function getPossibleImageSize(Document $document)
    {
        if ($document->type === DocumentType::SOCIAL_MEDIA_POST) {
            return self::getSocialMediaImageSize($document->getMeta('platform'));
        }

        if ($document->type === DocumentType::BLOG_POST) {
            return ['height' => 640, 'width' => 1536];
        }
    }
}
