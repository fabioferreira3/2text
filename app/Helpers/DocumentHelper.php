<?php

namespace App\Helpers;

use App\Enums\ChatGptModel;

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
        if (in_array($model, [ChatGptModel::GPT_3_TURBO->value, ChatGptModel::GPT_3_TURBO0301->value])) {
            return ($tokenUsage['total'] / 1000) * 0.002;
        } else if (in_array($model, [ChatGptModel::GPT_4->value, ChatGptModel::GPT_4_0314->value])) {
            return (($tokenUsage['prompt'] / 1000) * 0.03) + (($tokenUsage['completion'] / 1000) * 0.06);
        } else if (in_array($model, [ChatGptModel::GPT_4_32->value, ChatGptModel::GPT_4_32_0314->value])) {
            return (($tokenUsage['prompt'] / 1000) * 0.06) + (($tokenUsage['completion'] / 1000) * 0.12);
        } else {
            return 0;
        }
    }
}
