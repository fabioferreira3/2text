<?php

use App\Enums\DataType;
use App\Enums\SourceProvider;
use App\Jobs\EmbedSource;
use App\Models\Document;
use App\Models\User;

describe('EmbedSource job', function () {
    it('should embed from source', function ($dataType) {
        $user = User::factory()->create();
        $document = Document::factory()->create([
            'meta' => [
                'source' => SourceProvider::FREE_TEXT->value,
                'user_id' => $user->id
            ]
        ]);
        $job = new EmbedSource(
            $document,
            [
                'data_type' => $dataType,
                'source' => fake()->url()
            ]
        );
        $serialized = serialize($job);
        $response = $job->handle();
        expect($serialized)->toBeString();
        expect($response)->toBe('source embedded!');

        $uniqueId = $job->uniqueId();
        expect($uniqueId)->toBe('embed_document_' . $dataType . '_' . $document->id);
    })->with(DataType::getValues());
});
