<?php

namespace App\Enums;

enum LanguageModels: string
{
    case GPT_3_TURBO = 'gpt-3.5-turbo';
    case GPT_3_TURBO0301 = 'gpt-3.5-turbo-0301';
    case GPT_4 = 'gpt-4';
    case GPT_4_0314 = 'gpt-4-0314';
    case GPT_4_32 = 'gpt-4-32k';
    case GPT_4_32_0314 = 'gpt-4-32k-0314';
    case WHISPER = 'whisper';
}
