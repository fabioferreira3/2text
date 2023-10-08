<?php

namespace App\Enums;

enum LanguageModels: string
{
    case GPT_3_TURBO = 'gpt-3.5-turbo';
    case GPT_3_TURBO0301 = 'gpt-3.5-turbo-0301';
    case GPT_3_TURBO0613 = 'gpt-3.5-turbo-0613';
    case GPT_3_TURBO_16 = 'gpt-3.5-turbo-16k';
    case GPT_3_TURBO_16_0613 = 'gpt-3.5-turbo-16k-0613';
    case GPT_4 = 'gpt-4';
    case GPT_4_0314 = 'gpt-4-0314';
    case GPT_4_0613 = 'gpt-4-0613';
    case GPT_4_32 = 'gpt-4-32k';
    case GPT_4_32_0314 = 'gpt-4-32k-0314';
    case GPT_4_32_0613 = 'gpt-4-32k-0613';
    case ELEVEN_LABS = 'elevenlabs';
    case POLLY = 'aws_polly';
    case WHISPER = 'whisper';
}
