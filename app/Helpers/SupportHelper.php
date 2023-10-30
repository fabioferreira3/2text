<?php

namespace App\Helpers;

use App\Enums\AIModel;
use App\Models\ShortLink;
use Illuminate\Support\Facades\Validator;
use Talendor\StabilityAI\Enums\StabilityAIEngine;

class SupportHelper
{
    public static function calculateModelCosts(string $model, array $tokenUsage)
    {
        if (in_array($model, [
            AIModel::GPT_3_TURBO->value,
            AIModel::GPT_3_TURBO0301->value,
            AIModel::GPT_3_TURBO0613->value
        ])) {
            return (($tokenUsage['prompt'] / 1000) * 0.0015) + (($tokenUsage['completion'] / 1000) * 0.002);
        } elseif (in_array($model, [
            AIModel::GPT_3_TURBO_16->value,
            AIModel::GPT_3_TURBO_16_0613->value
        ])) {
            return (($tokenUsage['prompt'] / 1000) * 0.003) + (($tokenUsage['completion'] / 1000) * 0.004);
        } elseif (in_array($model, [
            AIModel::GPT_4->value,
            AIModel::GPT_4_0314->value,
            AIModel::GPT_4_0613->value
        ])) {
            return (($tokenUsage['prompt'] / 1000) * 0.03) + (($tokenUsage['completion'] / 1000) * 0.06);
        } elseif (in_array($model, [
            AIModel::GPT_4_32->value,
            AIModel::GPT_4_32_0314->value,
            AIModel::GPT_4_32_0613->value
        ])) {
            return (($tokenUsage['prompt'] / 1000) * 0.06) + (($tokenUsage['completion'] / 1000) * 0.12);
        } elseif (in_array($model, [AIModel::WHISPER->value])) {
            return $tokenUsage['audio_length'] * 0.006;
        } elseif (in_array($model, [AIModel::POLLY->value])) {
            return $tokenUsage['char_count'] * 0.000016;
        } elseif (in_array($model, [StabilityAIEngine::SD_XL_V_1->value])) {
            return 0.08;
        } else {
            return 0;
        }
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
}
