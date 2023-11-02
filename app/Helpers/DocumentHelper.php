<?php

namespace App\Helpers;

use App\Enums\AIModel;
use Talendor\StabilityAI\Enums\StabilityAIEngine;

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

    public static function calculateModelCosts(string $model, array $tokenUsage)
    {
        if (in_array($model, [AIModel::GPT_3_TURBO->value, AIModel::GPT_3_TURBO0301->value, AIModel::GPT_3_TURBO0613->value])) {
            return (($tokenUsage['prompt'] / 1000) * 0.0015) + (($tokenUsage['completion'] / 1000) * 0.002);
        } else if (in_array($model, [AIModel::GPT_3_TURBO_16->value, AIModel::GPT_3_TURBO_16_0613->value])) {
            return (($tokenUsage['prompt'] / 1000) * 0.003) + (($tokenUsage['completion'] / 1000) * 0.004);
        } else if (in_array($model, [AIModel::GPT_4->value, AIModel::GPT_4_0314->value, AIModel::GPT_4_0613->value])) {
            return (($tokenUsage['prompt'] / 1000) * 0.03) + (($tokenUsage['completion'] / 1000) * 0.06);
        } else if (in_array($model, [AIModel::GPT_4_32->value, AIModel::GPT_4_32_0314->value, AIModel::GPT_4_32_0613->value])) {
            return (($tokenUsage['prompt'] / 1000) * 0.06) + (($tokenUsage['completion'] / 1000) * 0.12);
        } else if (in_array($model, [AIModel::WHISPER->value])) {
            return $tokenUsage['audio_length'] * 0.006;
        } else if (in_array($model, [AIModel::POLLY->value])) {
            return $tokenUsage['char_count'] * 0.000016;
        } else if (in_array($model, [StabilityAIEngine::SD_XL_V_1->value])) {
            return 0.08;
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
        $whitespaceChars = ["\n", "\r", "\t", "\v", "\f"];
        foreach ($whitespaceChars as $char) {
            $cleanedText = str_replace($char, "", $text);
        }
        $sanitizedText = strip_tags($cleanedText);
        return preg_split('/(\\.|\?|!)/', $sanitizedText, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
    }

    public static function splitSentencesIntoArray(array $sentences)
    {
        $array = [];
        for ($i = 0; $i < count($sentences); $i += 2) {
            $array[] = [$sentences[$i], $sentences[$i + 1] ?? '.'];
        }
        return $array;
    }

    public static function chunkTextSentences($text, $amount = 80)
    {
        $sentences = preg_split('/(?<=[.!?])\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        $sentenceChunks = array_chunk($sentences, $amount);

        return array_map(function ($chunk) {
            return implode(' ', $chunk);
        }, $sentenceChunks);
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
