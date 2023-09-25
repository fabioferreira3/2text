<?php

namespace App\Helpers;

class MediaHelper
{
    public static function socialMediaImageSize(string $platform)
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
}
