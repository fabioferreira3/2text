<?php

namespace App\Enums;

enum AIModel: string
{
    case ASSEMBLY_AI = 'assembly-ai';
    case DALL_E_3 = 'dall-e-3';
    case DALL_E_2 = 'dall-e-2';
    case ELEVEN_LABS = 'elevenlabs';
    case ESRERGAN = 'esrgan-v1-x2plus';
    case GPT_3_TURBO = 'gpt-3.5-turbo';
    case GPT_3_TURBO1106 = 'gpt-3.5-turbo-1106';
    case GPT_3_TURBO_16 = 'gpt-3.5-turbo-16k';
    case GPT_4 = 'gpt-4';
    case GPT_4_32 = 'gpt-4-32k';
    case GPT_4_TURBO = 'gpt-4-0125-preview';
    case GPT_4_1106 = 'gpt-4-1106-preview';
    case GPT_4_VISION = 'gpt-4-vision-preview';
    case INPAINTING_V1 = 'stable-inpainting-v1-0';
    case INPAINTING_512_V2 = 'stable-inpainting-512-v2-0';
    case POLLY = 'aws_polly';
    case SD_XL_V_09 = 'stable-diffusion-xl-1024-v0-9';
    case SD_XL_V_1 = 'stable-diffusion-xl-1024-v1-0';
    case SD_XL_BETA_V2_2_2 = 'stable-diffusion-xl-beta-v2-2-2';
    case SD_V1 = 'stable-diffusion-v1';
    case SV_V1_5 = 'stable-diffusion-v1-5';
    case SD_512_V2 = 'stable-diffusion-512-v2-0';
    case SD_512_V2_1 = 'stable-diffusion-512-v2-1';
    case SD_768_V2 = 'stable-diffusion-768-v2-0';
    case SD_768_V2_1 = 'stable-diffusion-768-v2-1';
    case SD_DEPTH_V2 = 'stable-diffusion-depth-v2-0';
    case SD_X4_UPSCALER = 'stable-diffusion-x4-latent-upscaler';
    case WHISPER = 'whisper';

    public static function getValues(): array
    {
        return collect(self::cases())->flatMap(fn ($type) => [$type->value])->toArray();
    }
}
