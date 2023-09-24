<?php

namespace App\Repositories;

use App\Enums\MediaType;
use App\Models\Account;
use App\Models\MediaFile;
use Illuminate\Support\Facades\Storage;
use Talendor\StabilityAI\Enums\StabilityAIEngine;

class MediaRepository
{
    public static function storeImage(Account $account, $fileParams)
    {
        $fileName = 'ai-images/' . $fileParams['fileName'];
        Storage::disk('s3')->put($fileName, $fileParams['imageData']);
        $account->mediaFiles()->save(new MediaFile([
            'file_name' => $fileName,
            'type' => MediaType::IMAGE,
            'model' => StabilityAIEngine::SD_XL_V_1->value,
            'meta' => $fileParams['meta'] ?? []
        ]));
    }
}
