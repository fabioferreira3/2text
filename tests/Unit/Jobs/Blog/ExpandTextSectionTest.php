<?php

use App\Enums\DocumentTaskEnum;
use App\Enums\DocumentType;
use App\Enums\Language;
use App\Jobs\DispatchDocumentTasks;
use App\Jobs\ExpandTextSection;
use App\Models\Document;
use App\Models\User;
use App\Repositories\DocumentRepository;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->document = Document::factory()->create([
        'type' => DocumentType::BLOG_POST->value,
        'language' => Language::ENGLISH->value,
        'meta' => [
            'user_id' => $this->user->id
        ]
    ]);
});

describe(
    'Blog - ExpandTextSection job',
    function () {
        it('dispatches the task', function () {
            DocumentRepository::createTask(
                $this->document->id,
                DocumentTaskEnum::EXPAND_TEXT_SECTION,
                [
                    'process_id' => Str::uuid(),
                    'order' => 1,
                    'meta' => [
                        'text_section' => 'This is a text section',
                        'section_key' => 'section_key',
                        'keyword' => 'keyword',
                        'query_embedding' => false,
                        'collection_name' => '',
                    ]
                ]
            );
            $job = new DispatchDocumentTasks($this->document);
            $job->handle();

            Bus::assertDispatched(ExpandTextSection::class);
        });
    }
)->group('blog');
