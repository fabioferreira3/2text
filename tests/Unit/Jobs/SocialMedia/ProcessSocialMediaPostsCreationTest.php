<?php

use App\Enums\DocumentTaskEnum;
use App\Enums\DocumentType;
use App\Enums\SourceProvider;
use App\Jobs\DispatchDocumentTasks;
use App\Jobs\SocialMedia\ProcessSocialMediaPostsCreation;
use App\Models\Document;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Str;

beforeEach(function () {
    Bus::fake([DispatchDocumentTasks::class]);
});

describe('Social Media - ProcessSocialMediaPostsCreationTest job', function () {
    it('can be serialized', function () {
        $document = Document::factory()->create([
            'type' => DocumentType::SOCIAL_MEDIA_GROUP->value,
            'meta' => []
        ]);
        $job = new ProcessSocialMediaPostsCreation($document, []);
        $serialized = serialize($job);
        expect($serialized)->toBeString();
    });

    it('registers creation tasks for each platform', function ($generateImg, $imgPrompt) {
        $platforms = ['Facebook' => true, 'Twitter' => true];
        $document = Document::factory()->create([
            'type' => DocumentType::SOCIAL_MEDIA_GROUP->value,
            'meta' => [
                'source' => SourceProvider::FREE_TEXT->value,
                'max_words_count' => 250,
                'context' => fake()->words(1100, true),
                'platforms' => $platforms,
                'generate_img' => $generateImg,
                'img_prompt' => $imgPrompt
            ]
        ]);

        $job = new ProcessSocialMediaPostsCreation($document, [
            'query_embedding' => true,
            'platforms' => $document->getMeta('platforms')
        ]);
        $job->handle();

        foreach ($platforms as $platform => $value) {
            $this->assertDatabaseHas('documents', [
                'parent_document_id' => $document->id,
                'type' => DocumentType::SOCIAL_MEDIA_POST->value,
                'account_id' => $document->account_id,
                'meta->platform' => Str::of($platform)->lower()
            ]);

            $this->assertDatabaseHas('document_tasks', [
                'name' => DocumentTaskEnum::CREATE_SOCIAL_MEDIA_POST->value,
                'job' => DocumentTaskEnum::CREATE_SOCIAL_MEDIA_POST->getJob(),
                'process_id' => $job->textProcessId,
                'meta->platform' => Str::of($platform)->lower(),
                'meta->query_embedding' => true,
                'order' => 1
            ]);

            $this->assertDatabaseHas('document_tasks', [
                'name' => DocumentTaskEnum::REGISTER_FINISHED_PROCESS->value,
                'job' => DocumentTaskEnum::REGISTER_FINISHED_PROCESS->getJob(),
                'process_id' => $job->textProcessId,
                'meta->silently' => true,
                'order' => 2
            ]);

            if ($generateImg) {
                $this->assertDatabaseHas('document_tasks', [
                    'name' => DocumentTaskEnum::GENERATE_IMAGE->value,
                    'job' => DocumentTaskEnum::GENERATE_IMAGE->getJob(),
                    'process_id' => $job->imageProcessId,
                    'meta->process_id' => $job->imageProcessId,
                    'meta->prompt' => $imgPrompt,
                    'meta->add_content_block' => true,
                    'order' => 1
                ]);
                $this->assertDatabaseHas('document_tasks', [
                    'name' => DocumentTaskEnum::REGISTER_FINISHED_PROCESS->value,
                    'job' => DocumentTaskEnum::REGISTER_FINISHED_PROCESS->getJob(),
                    'process_id' => $job->imageProcessId,
                    'meta->silently' => true,
                    'order' => 2
                ]);
            }

            Bus::assertDispatched(DispatchDocumentTasks::class);
        }
    })->with([[false, null], [true, 'some image prompt']]);
})->group('socialmedia');
