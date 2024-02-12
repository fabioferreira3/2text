<?php

use App\Enums\DataType;
use App\Enums\SourceProvider;
use App\Jobs\EmbedSource;
use App\Jobs\Paraphraser\ParaphraseText;
use App\Models\Document;
use App\Models\User;

describe('ParaphraseText job', function () {
    it('should embed from source', function ($dataType) {
        $document = Document::factory()->create([
            'meta' => [
                'source' => SourceProvider::FREE_TEXT->value
            ]
        ]);
        $job = new ParaphraseText(
            $document,
            [
                'meta' => [
                    'tone' => 'formal',
                    'add_content_block' => true,
                    'sentence_order' => 1,
                    'text' => 'This is a test sentence.'
                ]
            ]
        );
        $response = $job->handle();
        expect($response)->toBe('source embedded!');

        $uniqueId = $job->uniqueId();
        expect($uniqueId)->toBe('embed_document_' . $dataType . '_' . $document->id);
    })->with(DataType::getValues());
})->group('paraphraser');
