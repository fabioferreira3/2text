<?php

namespace App\Repositories;

use App\Enums\MediaType;
use App\Models\Account;
use App\Models\MediaFile;
use Illuminate\Support\Facades\Storage;

class MediaRepository
{
    public static function newImage(Account $account, array $fileParams)
    {
        $account->mediaFiles()->save(new MediaFile([
            'file_url' => $fileParams['file_url'],
            'file_path' => $fileParams['file_path'],
            'type' => MediaType::IMAGE,
            'meta' => [
                'size' => $fileParams['file_size'],
                'width' => $fileParams['file_width'],
                'height' => $fileParams['file_height'],
                'extension' => $fileParams['file_extension'],
                'publicId' => $fileParams['file_public_id'],
                ...$fileParams['meta'] ?? []
            ]
        ]));
    }

    public static function storeImage(Account $account, $fileParams)
    {
        $fileParams['fileName'] = 'ai-images/' . $fileParams['fileName'];
        Storage::disk('s3')->put($fileParams['fileName'], $fileParams['imageData']);

        self::optimizeAndStore($account, $fileParams);
    }

    public static function optimizeAndStore(Account $account, $fileParams)
    {
        $originalFileUrl = Storage::temporaryUrl($fileParams['fileName'], now()->addMinutes(5));
        $uploadedFile = cloudinary()->upload($originalFileUrl);
        self::newImage($account, [
            'file_url' => $uploadedFile->getSecurePath(),
            'file_path' => $fileParams['fileName'],
            'file_size' => $uploadedFile->getSize(),
            'file_width' => $uploadedFile->getWidth(),
            'file_height' => $uploadedFile->getHeight(),
            'file_extension' => $uploadedFile->getExtension(),
            'file_public_id' => $uploadedFile->getPublicId(),
            'meta' => [...$fileParams['meta'] ?? []]
        ]);
    }
}
