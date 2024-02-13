<?php

use App\Enums\DocumentTaskEnum;
use App\Enums\SourceProvider;
use App\Events\Paraphraser\TextParaphrased;
use App\Interfaces\ChatGPTFactoryInterface;
use App\Jobs\Paraphraser\ParaphraseText;
use App\Jobs\RegisterAppUsage;
use App\Jobs\RegisterUnitsConsumption;
use App\Models\Document;
use App\Models\DocumentTask;
use App\Packages\OpenAI\ChatGPT;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->processId = Str::uuid();
    $this->document = Document::factory()->create([
        'meta' => [
            'source' => SourceProvider::FREE_TEXT->value,
            'user_id' => $this->authUser->id
        ]
    ]);
    $this->documentTask = DocumentTask::factory()->create([
        'document_id' => $this->document->id,
        'process_id' => $this->processId
    ]);
});

describe('ParaphraseText job', function () {
    it('paraphrases text and registers events', function ($addContentBlock) {
        Event::fake([TextParaphrased::class]);

        expect($this->documentTask->status)->toBe('ready');

        $job = new ParaphraseText(
            $this->document,
            [
                'tone' => 'formal',
                'add_content_block' => $addContentBlock,
                'sentence_order' => 4,
                'text' => 'This is a test sentence.',
                'process_id' => $this->processId,
                'task_id' => $this->documentTask->id
            ]
        );
        $job->handle();

        if ($addContentBlock) {
            $this->assertDatabaseHas('document_content_blocks', [
                'document_id' => $this->document->id,
                'type' => 'text',
                'content' => 'AI content generated',
                'prompt' => null,
                'order' => 4
            ]);
        } else {
            $this->assertDatabaseMissing('document_content_blocks', [
                'document_id' => $this->document->id,
                'type' => 'text',
                'content' => 'AI content generated',
                'prompt' => null,
                'order' => 4
            ]);
        }

        Bus::assertDispatched(RegisterUnitsConsumption::class, function ($job) {
            return $job->account->id === $this->document->account_id &&
                $job->type === 'words_generation' &&
                $job->meta['word_count'] === 3 &&
                $job->meta['document_id'] === $this->document->id &&
                $job->meta['job'] === DocumentTaskEnum::PARAPHRASE_TEXT->value;
        });
        Bus::assertDispatched(RegisterAppUsage::class);
        Event::assertDispatched(TextParaphrased::class, function ($event) {
            return $event->document->id === $this->document->id &&
                $event->params['user_id'] === $this->document->getMeta('user_id');
        });

        expect($this->documentTask->fresh()->status)->toBe('finished');
    })->with([true, false]);

    it('handles exceptions', function () {
        Event::fake([TextParaphrased::class]);
        expect($this->documentTask->status)->toBe('ready');

        $chatGpt = Mockery::mock(ChatGPT::class);
        $chatGpt->shouldReceive('request')->andThrow(new Exception('Error'));
        $mockChatGPTFactorys = Mockery::mock(ChatGPTFactoryInterface::class);
        $mockChatGPTFactorys->shouldReceive('make')->andReturn($chatGpt);
        $this->app->instance(ChatGPTFactoryInterface::class, $mockChatGPTFactorys);

        $job = new ParaphraseText(
            $this->document,
            [
                'tone' => 'formal',
                'add_content_block' => false,
                'sentence_order' => 4,
                'text' => 'This is a test sentence.',
                'process_id' => $this->processId,
                'task_id' => $this->documentTask->id
            ]
        );
        $job->handle();

        $this->assertDatabaseMissing('document_content_blocks', [
            'document_id' => $this->document->id,
            'type' => 'text',
            'content' => 'AI content generated',
            'prompt' => null,
            'order' => 4
        ]);
        Bus::assertNotDispatched(RegisterUnitsConsumption::class);
        Bus::assertNotDispatched(RegisterAppUsage::class);
        Event::assertNotDispatched(TextParaphrased::class);

        expect($this->documentTask->fresh()->status)->toBe('failed');
    });
})->group('paraphraser');
