<?php

use App\Enums\DataType;
use App\Enums\DocumentTaskEnum;
use App\Enums\SourceProvider;
use App\Jobs\DispatchDocumentTasks;
use App\Jobs\SocialMedia\ProcessSocialMediaPosts;
use App\Models\Document;
use App\Models\DocumentTask;
use Illuminate\Support\Facades\Bus;


describe('Social Media - ProcessSocialMediaPosts job', function () {
    it('registers remove embedding task', function () {
        Bus::fake([DispatchDocumentTasks::class, ProcessSocialMediaPosts::class]);

        $document = Document::factory()->create([
            'meta' => [
                'source' => SourceProvider::PDF->value,
                'max_words_count' => 250
            ]
        ]);

        $job = new ProcessSocialMediaPosts($document, [
            "Linkedin" => false,
            "Facebook" => true,
            "Instagram" => false,
            "Twitter" => false
        ]);
        $job->handle();

        $removeEmbedTask = DocumentTask::where('process_id', $job->processId)
            ->where('name', DocumentTaskEnum::REMOVE_EMBEDDINGS->value)->first();
        expect($removeEmbedTask)->toMatchArray([
            'name' => DocumentTaskEnum::REMOVE_EMBEDDINGS->value,
            'job' => DocumentTaskEnum::REMOVE_EMBEDDINGS->getJob(),
            'document_id' => $document->id,
            'process_id' => $job->processId,
            'order' => 1,
            'meta' => [
                'collection_name' => $document->id
            ]
        ]);

        Bus::assertDispatched(DispatchDocumentTasks::class, function ($job) use ($document) {
            return $job->document->id === $document->id;
        });
    });

    it('registers free text tasks', function () {
        Bus::fake([
            DispatchDocumentTasks::class,
            ProcessSocialMediaPosts::class
        ]);

        $document = Document::factory()->create([
            'meta' => [
                'source' => SourceProvider::FREE_TEXT->value,
                'max_words_count' => 250,
                'context' => fake()->words(1100, true)
            ]
        ]);

        $job = new ProcessSocialMediaPosts($document, [
            "Linkedin" => false,
            "Facebook" => true,
            "Instagram" => false,
            "Twitter" => false
        ]);
        $job->handle();

        $embedTask = DocumentTask::where('process_id', $job->processId)
            ->where('name', DocumentTaskEnum::EMBED_SOURCE->value)->firstOrFail();

        expect($embedTask)->toMatchArray([
            'name' => DocumentTaskEnum::EMBED_SOURCE->value,
            'job' => DocumentTaskEnum::EMBED_SOURCE->getJob(),
            'document_id' => $document->id,
            'process_id' => $job->processId,
            'order' => 2,
            'meta' => [
                'data_type' => DataType::TEXT->value,
                'source' => $document->getMeta('context'),
                'collection_name' => $document->id
            ]
        ]);

        Bus::assertDispatched(DispatchDocumentTasks::class, function ($job) use ($document) {
            return $job->document->id === $document->id;
        });
    });

    it('registers embedding task for each URL', function () {
        Bus::fake([DispatchDocumentTasks::class, ProcessSocialMediaPosts::class]);

        $document = Document::factory()->create([
            'meta' => [
                'source' => SourceProvider::WEBSITE_URL->value,
                'source_urls' => [
                    fake()->url(),
                    fake()->url(),
                    fake()->url(),
                    fake()->url(),
                    fake()->url()
                ]
            ]
        ]);

        $job = new ProcessSocialMediaPosts($document, [
            "Linkedin" => false,
            "Facebook" => true,
            "Instagram" => false,
            "Twitter" => false
        ]);
        $job->handle();
        $initialOrder = 2;

        $embedTasks = DocumentTask::where('process_id', $job->processId)
            ->where('name', DocumentTaskEnum::EMBED_SOURCE->value)->get();

        foreach ($embedTasks as $embedTask) {
            $this->assertEquals($embedTask->meta['data_type'], DataType::WEB_PAGE->value);
            $this->assertEquals($embedTask->meta['collection_name'], $document->id);
            $this->assertTrue(in_array(
                $embedTask->meta['source'],
                $document->getMeta('source_urls')
            ));
            expect($embedTask)->toMatchArray([
                'name' => DocumentTaskEnum::EMBED_SOURCE->value,
                'job' => DocumentTaskEnum::EMBED_SOURCE->getJob(),
                'document_id' => $document->id,
                'process_id' => $job->processId,
                'order' => $initialOrder
            ]);
            $initialOrder++;
        }


        Bus::assertDispatched(DispatchDocumentTasks::class, function ($job) use ($document) {
            return $job->document->id === $document->id;
        });
    });

    it('registers embedding task for each file', function ($sourceType) {
        Bus::fake([DispatchDocumentTasks::class, ProcessSocialMediaPosts::class]);

        $sourceFilePath = fake()->filePath();
        $document = Document::factory()->create([
            'meta' => [
                'source' => $sourceType,
                'source_file_path' => $sourceFilePath
            ]
        ]);

        $job = new ProcessSocialMediaPosts($document, [
            "Linkedin" => false,
            "Facebook" => true,
            "Instagram" => false,
            "Twitter" => false
        ]);
        $job->handle();
        $initialOrder = 2;

        $embedTasks = DocumentTask::where('process_id', $job->processId)
            ->where('name', DocumentTaskEnum::EMBED_SOURCE->value)->get();

        foreach ($embedTasks as $embedTask) {
            $this->assertEquals($embedTask->meta['collection_name'], $document->id);
            $this->assertEquals(
                $embedTask->meta['source'],
                $document->getMeta('source_file_path')
            );
            expect($embedTask)->toMatchArray([
                'name' => DocumentTaskEnum::EMBED_SOURCE->value,
                'job' => DocumentTaskEnum::EMBED_SOURCE->getJob(),
                'document_id' => $document->id,
                'process_id' => $job->processId,
                'order' => $initialOrder
            ]);
            $initialOrder++;
        }


        Bus::assertDispatched(DispatchDocumentTasks::class, function ($job) use ($document) {
            return $job->document->id === $document->id;
        });
    })->with([
        SourceProvider::PDF->value,
        SourceProvider::DOCX->value,
        SourceProvider::CSV->value,
    ]);

    it('registers extracts and embeds audio tasks', function () {
        Bus::fake([DispatchDocumentTasks::class, ProcessSocialMediaPosts::class]);

        $document = Document::factory()->create([
            'meta' => [
                'source' => SourceProvider::YOUTUBE->value,
                'source_urls' => [
                    fake()->url()
                ]
            ]
        ]);

        $job = new ProcessSocialMediaPosts($document, [
            "Linkedin" => false,
            "Facebook" => true,
            "Instagram" => false,
            "Twitter" => false
        ]);
        $job->handle();
        $initialOrder = 2;

        $embedTasks = DocumentTask::where('process_id', $job->processId)
            ->where('name', DocumentTaskEnum::EXTRACT_AND_EMBED_AUDIO->value)->get();

        foreach ($embedTasks as $embedTask) {
            $this->assertEquals($embedTask->meta['collection_name'], $document->id);
            $this->assertTrue(in_array(
                $embedTask->meta['source_url'],
                $document->getMeta('source_urls')
            ));
            expect($embedTask)->toMatchArray([
                'name' => DocumentTaskEnum::EXTRACT_AND_EMBED_AUDIO->value,
                'job' => DocumentTaskEnum::EXTRACT_AND_EMBED_AUDIO->getJob(),
                'document_id' => $document->id,
                'process_id' => $job->processId,
                'order' => $initialOrder
            ]);
            $initialOrder++;
        }


        Bus::assertDispatched(DispatchDocumentTasks::class, function ($job) use ($document) {
            return $job->document->id === $document->id;
        });
    });
});
