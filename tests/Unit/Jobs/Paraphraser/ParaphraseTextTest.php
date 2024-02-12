<?php

use App\Enums\SourceProvider;
use App\Events\Paraphraser\TextParaphrased;
use App\Jobs\Paraphraser\ParaphraseText;
use App\Jobs\RegisterAppUsage;
use App\Jobs\RegisterUnitsConsumption;
use App\Models\Document;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;

describe('ParaphraseText job', function () {
    it('paraphrases text and registers events', function () {
        Event::fake([TextParaphrased::class]);
        $processId = Str::uuid();
        $document = Document::factory()->create([
            'meta' => [
                'source' => SourceProvider::FREE_TEXT->value,
                'user_id' => $this->authUser->id
            ]
        ]);
        $job = new ParaphraseText(
            $document,
            [
                'tone' => 'formal',
                'add_content_block' => true,
                'sentence_order' => 1,
                'text' => 'This is a test sentence.',
                'process_id' => $processId
            ]
        );
        $job->handles();
        Bus::assertDispatched(RegisterUnitsConsumption::class);
        Bus::assertDispatched(RegisterAppUsage::class);
        Event::assertDispatched(TextParaphrased::class);
    });
})->group('paraphraser');
