<?php

namespace App\Helpers;

use App\Enums\DocumentType;
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

    public static function getPossibleImageSize(DocumentContentBlock $contentBlock)
    {
        if ($contentBlock->document->type === DocumentType::SOCIAL_MEDIA_POST) {
            return self::getSocialMediaImageSize($contentBlock->document->getMeta('platform'));
        }

        if ($contentBlock->document->type === DocumentType::BLOG_POST) {
            return ['height' => 640, 'width' => 1536];
        }
    }
}
