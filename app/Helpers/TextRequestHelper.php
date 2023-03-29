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

    public static function parseOutlineToRawStructure(string $text)
    {
        $lines = explode(PHP_EOL, $text);
        $result = [];
        $currentSubheaderIndex = -1;

        foreach ($lines as $line) {
            $trimmed = trim($line);

            if (preg_match('/^[IVX]+\..+/', $trimmed)) {
                $currentSubheader = preg_replace('/^[IVX]+\.\s/', '', $trimmed);
                $result[] = [
                    'subheader' => $currentSubheader,
                    'content' => []
                ];
                $currentSubheaderIndex++;
            } elseif (preg_match('/^[A-Z]\..+/', $trimmed)) {
                $paragraph = preg_replace('/^[A-Z]\.\s/', '', $trimmed);
                $result[$currentSubheaderIndex]['content'][] = '<h3>' . $paragraph . '</h3>';
            }
        }

        return $result;
    }

    public static function parseExpandedTextToRawStructure(string $text)
    {
        $lines = explode(PHP_EOL, $text);
        $result = [];
        $currentSection = -1;

        foreach ($lines as $line) {
            $trimmed = trim($line);

            if (preg_match('/^[IVX]+\..+/', $trimmed)) {
                $subheader = preg_replace('/^[IVX]+\.\s/', '', $trimmed);
                $result[] = [
                    'subheader' => $subheader,
                    'content' => []
                ];
                $currentSection++;
            } elseif (preg_match('/^<p>.+<\/p>$/', $trimmed)) {
                $result[$currentSection]['content'][] = $trimmed;
            }
        }

        return $result;
    }

    public static function parseHtmlTagsToRawStructure($html)
    {
        preg_match_all('/<(p|ul|ol)>.*?<\/(p|ul|ol)>/s', $html, $matches);
        return $matches[0];
    }
}
