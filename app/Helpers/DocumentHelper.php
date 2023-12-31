<?php

namespace App\Helpers;

class DocumentHelper
{
    public static function parseOutlineToRawStructure(string $text)
    {
        $lines = explode(PHP_EOL, $text);
        $result = [];
        $currentSubheaderIndex = -1;

        foreach ($lines as $line) {
            $trimmed = trim($line);

            // Match lines that start with a number followed by a period
            if (preg_match('/^\d+\..+/', $trimmed)) {
                $currentSubheader = preg_replace('/^\d+\.\s/', '', $trimmed);
                $result[] = [
                    'subheader' => $currentSubheader,
                    'content' => ''
                ];
                $currentSubheaderIndex++;
            }
            // Match lines that start with an uppercase letter followed by a period
            elseif (preg_match('/^[A-Z]\..+/', $trimmed)) {
                $subtopic = preg_replace('/^[A-Z]\.\s/', '', $trimmed);

                if ($currentSubheaderIndex >= 0) {
                    // If there's already content, add a space before the next subtopic
                    if (!empty($result[$currentSubheaderIndex]['content'])) {
                        $result[$currentSubheaderIndex]['content'] .= ' ';
                    }
                    $result[$currentSubheaderIndex]['content'] .= $subtopic . '.';
                }
            }
        }

        return $result;
    }

    public static function parseHtmlTagsToRawStructure($html)
    {
        $regex = '/<h2>(.*?)<\/h2>(.*?)((?=<h2>)|$)/s';
        preg_match_all($regex, $html, $matches, PREG_SET_ORDER);

        $output = [];
        foreach ($matches as $match) {
            $subheader = $match[1];
            $content = $match[2];
            $output[] = [
                'subheader' => preg_replace('/^([IVXLCDM]+|[A-Z]+)\.\s+/', '', $subheader),
                'content' => preg_replace('/<h3>[A-Za-z]\. (.*?)<\/h3>/', '<h3>$1</h3>', $content)
            ];
        }

        return $output;
    }

    public static function breakTextIntoSentences($text)
    {
        $sentences = self::splitIntoSentences($text);
        $sentencesArray = self::splitSentencesIntoArray($sentences);
        $originalSentencesArray = collect($sentencesArray)->map(function ($sentenceStructure, $idx) {
            return ['sentence_order' => $idx + 1, 'text' => trim($sentenceStructure[0] . $sentenceStructure[1])];
        });

        $filteredSentencesArray = $originalSentencesArray->filter(function ($item) {
            return !(strlen($item['text']) === 1 && ctype_punct($item['text']));
        });

        return $filteredSentencesArray->values();
    }

    public static function splitIntoSentences($text)
    {
        $whitespaceChars = ["\n", "\r", "\t", "\v", "\f"];
        $cleanedText = $text;
        foreach ($whitespaceChars as $char) {
            $cleanedText = str_replace($char, " ", $cleanedText);
        }

        $sanitizedText = strip_tags($cleanedText);

        return preg_split('/([^!.?]+)([.!?]+|\.{3,})/', $sanitizedText, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
    }


    public static function splitSentencesIntoArray(array $sentences)
    {
        $array = [];
        for ($i = 0; $i < count($sentences); $i += 2) {
            $array[] = [$sentences[$i], $sentences[$i + 1] ?? '.'];
        }
        return $array;
    }

    public static function parseHtmlToArray($html)
    {
        $pattern = '/<(\w+)>([^<]+)<\/\1>/';
        preg_match_all($pattern, $html, $matches, PREG_SET_ORDER);

        $result = [];
        foreach ($matches as $match) {
            $result[] = [
                'tag' => $match[1],
                'content' => $match[2]
            ];
        }

        return $result;
    }
}
