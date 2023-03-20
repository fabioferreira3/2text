<?php

namespace App\Helpers;

class TextRequestHelper
{
    public static function parseLanguage(string $language)
    {
        switch ($language) {
            case 'en':
                return 'English';
            case 'pt':
                return 'Portuguese';
            default:
                return $language;
        }
    }

    public static function parseSource(string $source)
    {
        switch ($source) {
            case 'free_text':
                return 'Free text';
            case 'youtube':
                return 'Youtube';
            default:
                return $source;
        }
    }
}
