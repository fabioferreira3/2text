<?php

use App\Enums\Language;
use App\Jobs\RegisterProductUsage;
use App\Jobs\SummarizeContent;
use App\Jobs\Translation\TranslateTextBlock;
use App\Models\Document;
use App\Models\DocumentTask;
use Illuminate\Support\Facades\Bus;

beforeEach(function () {
    Bus::fake([RegisterProductUsage::class, TranslateTextBlock::class]);
});

function createDocument($queryEmbedding, $maxWordsCount, $targetLanguage)
{
    return Document::factory()->create([
        'meta' => [
            'query_embedding' => $queryEmbedding,
            'max_words_count' => $maxWordsCount,
            'target_language' => $targetLanguage
        ]
    ]);
}

function commonAssertions($document, $chatGptResponse)
{
    $contentBlock = $document->fresh()->contentBlocks()->first();
    expect($contentBlock)->toMatchArray([
        'document_id' => $document->id,
        'content' => 'AI content generated',
        'type' => 'text',
        'prompt' => null
    ]);

    Bus::assertDispatched(RegisterProductUsage::class, function ($job) use ($document, $chatGptResponse) {
        return $job->account->id === $document->account_id &&
            $job->params['model'] === $chatGptResponse['token_usage']['model'] &&
            $job->params['prompt'] === $chatGptResponse['token_usage']['prompt'] &&
            $job->params['completion'] === $chatGptResponse['token_usage']['completion'] &&
            $job->params['total'] === $chatGptResponse['token_usage']['total'] &&
            $job->params['meta']['document_id'] === $document->id;
    });

    Bus::assertDispatchedSync(TranslateTextBlock::class, function ($job) use ($document) {
        return $job->document->id === $document->id &&
            $job->contentBlock->id === $document->contentBlocks()->first()->id &&
            $job->params['target_language'] === Language::PORTUGUESE->value;
    });
}

describe('Summarize Content job', function () {
    it('summarizes and translates without embedding', function () {

        $document = createDocument(false, 250, Language::PORTUGUESE->value);
        $job = new SummarizeContent($document, [
            'content' => 'Content to be summarized',
            'query_embedding' => false,
            'max_words_count' => 250
        ], $this->generator);
        $job->handle();

        $contentBlock = $document->fresh()->contentBlocks()->first();
        expect($contentBlock)->toMatchArray([
            'document_id' => $document->id,
            'content' => 'AI content generated',
            'type' => 'text',
            'prompt' => null
        ]);

        $chatGptResponse = $this->aiModelResponseResponse;
        commonAssertions($document, $chatGptResponse);
    });

    it('summarizes and translates based on embeddings', function () {

        $document = createDocument(true, 250, Language::PORTUGUESE->value);

        $job = new SummarizeContent($document, [
            'content' => 'Content to be summarized',
            'query_embedding' => true,
            'collection_name' => $document->id,
            'max_words_count' => 250
        ], $this->generator);
        $job->handle();

        $chatGptResponse = $this->aiModelResponseResponse;
        commonAssertions($document, $chatGptResponse);
    });

    it('summarizes but don\'t translate', function () {

        $document = createDocument(false, 250, null);

        $job = new SummarizeContent($document, [
            'content' => 'Content to be summarized',
            'query_embedding' => true,
            'collection_name' => $document->id,
            'max_words_count' => 250
        ], $this->generator);
        $job->handle();

        Bus::assertNotDispatchedSync(TranslateTextBlock::class);
    });

    it('finishes the task', function () {

        $document = createDocument(false, 250, null);
        $task = DocumentTask::factory()->create([
            'document_id' => $document->id
        ]);
        expect($task->status)->toEqual('in_progress');

        $job = new SummarizeContent($document, [
            'task_id' => $task->id,
            'content' => 'Content to be summarized',
            'query_embedding' => true,
            'collection_name' => $document->id,
            'max_words_count' => 250
        ], $this->generator);
        $job->handle();

        $task->refresh();
        expect($task->status)->toEqual('finished');
    });
});
