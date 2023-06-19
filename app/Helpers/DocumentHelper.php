<?php

namespace App\Helpers;

use App\Enums\LanguageModels;

class DocumentHelper
{
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
                    'content' => ''
                ];
                $currentSubheaderIndex++;
            } elseif (preg_match('/^[A-Z]\..+/', $trimmed)) {
                $subtopic = preg_replace('/^[A-Z]\.\s/', '', $trimmed);
                $result[$currentSubheaderIndex]['content'] .= '<h3>' . $subtopic . '</h3>';
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

    public static function calculateModelCosts(string $model, array $tokenUsage)
    {
        if (in_array($model, [LanguageModels::GPT_3_TURBO->value, LanguageModels::GPT_3_TURBO0301->value, LanguageModels::GPT_3_TURBO0613->value])) {
            return (($tokenUsage['prompt'] / 1000) * 0.0015) + (($tokenUsage['completion'] / 1000) * 0.002);
        } else if (in_array($model, [LanguageModels::GPT_3_TURBO_16->value, LanguageModels::GPT_3_TURBO_16_0613->value])) {
            return (($tokenUsage['prompt'] / 1000) * 0.003) + (($tokenUsage['completion'] / 1000) * 0.004);
        } else if (in_array($model, [LanguageModels::GPT_4->value, LanguageModels::GPT_4_0314->value, LanguageModels::GPT_4_0613->value])) {
            return (($tokenUsage['prompt'] / 1000) * 0.03) + (($tokenUsage['completion'] / 1000) * 0.06);
        } else if (in_array($model, [LanguageModels::GPT_4_32->value, LanguageModels::GPT_4_32_0314->value, LanguageModels::GPT_4_32_0613->value])) {
            return (($tokenUsage['prompt'] / 1000) * 0.06) + (($tokenUsage['completion'] / 1000) * 0.12);
        } else if (in_array($model, [LanguageModels::WHISPER->value])) {
            return $tokenUsage['audio_length'] * 0.006;
        } else {
            return 0;
        }
    }

    public static function breakTextIntoSentences($text)
    {
        $sentences = self::splitIntoSentences($text);
        $sentencesArray = self::splitSentencesIntoArray($sentences);
        $originalSentencesArray = collect($sentencesArray)->map(function ($sentenceStructure, $idx) {
            return ['sentence_order' => $idx + 1, 'text' => $sentenceStructure[0] . $sentenceStructure[1]];
        });
        return $originalSentencesArray;
    }

    public static function splitIntoSentences($text)
    {
        return preg_split('/(\\.|\?|!)/', $text, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
    }

    public static function splitSentencesIntoArray(array $sentences)
    {
        $array = [];
        for ($i = 0; $i < count($sentences); $i += 2) {
            $array[] = [$sentences[$i], $sentences[$i + 1] ?? '.'];
        }
        return $array;
    }
}
