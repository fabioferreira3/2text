<?php

namespace App\Helpers;

use App\Models\Voice;
use Illuminate\Support\Facades\Storage;

class AudioHelper
{
    public static function getVoices()
    {
        return Voice::orderBy('name', 'ASC')->get()->map(function ($voice) {
            return [
                'id' => $voice->id,
                'value' => $voice->name,
                'label' => $voice->name,
                'url' => $voice->preview_url,
                'meta' => [
                    'age' => $voice->meta['age'] ?? null,
                    'gender' => $voice->meta['gender'] ?? null,
                    'description' => $voice->meta['description'] ?? null,
                ]
            ];
        });
    }

    public static function getAudioUrl($fileName)
    {
        return Storage::temporaryUrl($fileName, now()->addMinutes(30));
    }
}
