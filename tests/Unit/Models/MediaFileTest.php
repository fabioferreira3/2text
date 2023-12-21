<?php

namespace Tests\Unit\Models;

use App\Models\Account;
use App\Models\MediaFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

describe('MediaFile model', function () {
    it('gets signed url', function () {
        $file = MediaFile::factory()->create(['file_path' => 'path/to/file.jpg']);

        $signedUrl = $file->getSignedUrl();

        expect($signedUrl)->toContain('path/to/file.jpg');
    });

    it('scopes images', function () {
        $imageFile = MediaFile::factory()->create(['type' => 'image']);
        $audioFile = MediaFile::factory()->create(['type' => 'audio']);

        $images = MediaFile::images()->get();

        expect($images->contains($imageFile))->toBeTrue();
        expect($images->contains($audioFile))->toBeFalse();
    });

    it('converts to binary', function () {
        $file = MediaFile::factory()->create(['file_path' => 'path/to/file.jpg']);
        Storage::disk('s3')->put('path/to/file.jpg', 'file content');

        $binary = $file->toBinary();

        expect($binary)->toEqual('file content');
    });

    it('sets account_id on save', function () {
        $account = Account::factory()->create();
        Auth::shouldReceive('check')->andReturn(true);
        Auth::shouldReceive('user')->andReturn((object) ['account_id' => $account->id]);

        $file = MediaFile::factory()->create();

        expect($file->account_id)->toEqual($account->id);
    });
})->group('models');
