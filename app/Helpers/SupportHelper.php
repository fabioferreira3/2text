<?php

namespace App\Helpers;

use App\Enums\AIModel;
use App\Models\ShortLink;
use Illuminate\Support\Facades\Validator;
use Talendor\StabilityAI\Enums\StabilityAIEngine;

class SupportHelper
{
    public static function calculateModelCosts(string $model, array $params)
    {
        $cost = 0.0000000;
        if (in_array($model, [
            AIModel::GPT_3_TURBO->value
        ])) {
            $cost = (($params['prompt'] / 1000) * 0.0005) + (($params['completion'] / 1000) * 0.0015);
        } elseif (in_array($model, [
            AIModel::GPT_4->value
        ])) {
            $cost = (($params['prompt'] / 1000) * 0.03) + (($params['completion'] / 1000) * 0.06);
        } elseif (in_array($model, [
            AIModel::GPT_4_TURBO->value,
            AIModel::GPT_4_1106->value,
            AIModel::GPT_4_VISION->value
        ])) {
            $cost = (($params['prompt'] / 1000) * 0.01) + (($params['completion'] / 1000) * 0.03);
        } elseif (in_array($model, [
            AIModel::GPT_4_32->value
        ])) {
            $cost = (($params['prompt'] / 1000) * 0.06) + (($params['completion'] / 1000) * 0.12);
        } elseif (in_array($model, [AIModel::WHISPER->value])) {
            $cost = $params['audio_length'] * 0.0061;
        } elseif (in_array($model, [AIModel::ASSEMBLY_AI->value])) {
            $cost = $params['audio_length'] * 0.0061;
        } elseif (in_array($model, [AIModel::ELEVEN_LABS->value])) {
            $cost = $params['char_count'] * 0.00011;
        } elseif (in_array($model, [StabilityAIEngine::SD_XL_V_1->value])) {
            $cost = 0.08;
        } elseif (in_array($model, [AIModel::DALL_E_3->value])) {
            $cost = 0.04;
        }

        return number_format($cost, 7);
    }

    public static function formatCentsToDollars($cents)
    {
        $dollars = $cents / 100;
        return number_format($dollars, 2, '.', ',');
    }

    public static function subPercent(int $number, int $percentage)
    {
        return $number - (($number * $percentage) / 100);
    }

    public static function shortenLink(string $targetUrl, array $params): string
    {
        Validator::make(['link' => $targetUrl], [
            'link' => 'required|url'
        ])->validate();

        $shortLink = ShortLink::create([
            'account_id' => $params['account_id'],
            'expires_at' => $params['expires_at'] ?? now()->addMinutes(30),
            'target_url' => $targetUrl
        ]);

        return config('app.url') . "/link/" . $shortLink->link;
    }

    public static function getTimezones()
    {
        $timezones = [];
        foreach (\DateTimeZone::listIdentifiers() as $timezone) {
            $timezones[] = [
                'value' => $timezone,
                'label' => $timezone
            ];
        }

        return $timezones;
    }
}
