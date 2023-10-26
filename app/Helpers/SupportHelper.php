<?php

namespace App\Helpers;

use App\Enums\LanguageModels;
use Talendor\StabilityAI\Enums\StabilityAIEngine;

class SupportHelper
{
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
        } else if (in_array($model, [LanguageModels::POLLY->value])) {
            return $tokenUsage['char_count'] * 0.000016;
        } else if (in_array($model, [StabilityAIEngine::SD_XL_V_1->value])) {
            return 0.08;
        } else {
            return 0;
        }
    }
}
