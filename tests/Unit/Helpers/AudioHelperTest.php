<?php

use App\Helpers\AudioHelper;
use App\Models\Voice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake();
    Voice::factory()->createMany([
        ['name' => 'Voice A', 'preview_url' => 'url_a', 'meta' => ['age' => 20, 'gender' => 'male', 'description' => 'desc_a']],
        ['name' => 'Voice B', 'preview_url' => 'url_b', 'meta' => ['age' => 25, 'gender' => 'female', 'description' => 'desc_b']]
    ]);
});

describe('AudioHelper helper', function () {
    it('can retrieve voices ordered by name', function () {
        $voices = AudioHelper::getVoices();

        expect($voices)->toHaveCount(2);

        // Test the structure and data of the first item
        expect($voices[0])->toMatchArray([
            'id' => $voices[0]['id'],
            'value' => 'Voice A',
            'label' => 'Voice A',
            'url' => 'url_a',
            'meta' => [
                'age' => 20,
                'gender' => 'male',
                'description' => 'desc_a',
            ],
        ]);
    });

    it('can generate a temporary audio URL', function () {
        Carbon::setTestNow(now());

        Storage::shouldReceive('temporaryUrl')
            ->withArgs(function ($fileName, $expiration) {
                return $fileName === 'test_file.mp3' && $expiration->diffInMinutes(Carbon::now()) === 30;
            })
            ->andReturn('http://localhost/temporary/test_file.mp3');

        $url = AudioHelper::getAudioUrl('test_file.mp3');

        expect($url)->toBe('http://localhost/temporary/test_file.mp3');
    });
})->group('helpers');
