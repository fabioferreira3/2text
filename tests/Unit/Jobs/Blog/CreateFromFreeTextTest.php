<?php

use App\Enums\DocumentType;
use App\Enums\Language;
use App\Jobs\Blog\CreateFromFreeText;
use App\Jobs\Blog\RegisterCreationTasks;
use App\Jobs\DispatchDocumentTasks;
use App\Models\Document;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->document = Document::factory()->create([
        'type' => DocumentType::BLOG_POST->value,
        'language' => Language::ENGLISH->value,
        'meta' => []
    ]);
});

describe(
    'Blog - CreateFromFreeText job',
    function () {
        it('registers common tasks', function () {
            Bus::fake([DispatchDocumentTasks::class, RegisterCreationTasks::class]);
            $processId = Str::uuid();
            $job = new CreateFromFreeText($this->document, [
                'process_id' => $processId
            ]);
            $job->handle();

            Bus::assertDispatchedSync(RegisterCreationTasks::class, function ($job) use ($processId) {
                return
                    $job->document->id === $this->document->id &&
                    $job->params['next_order'] === 1 &&
                    $job->params['process_id'] == $processId;
            });
            Bus::assertDispatched(DispatchDocumentTasks::class, function ($job) {
                return $job->document->id === $this->document->id;
            });
        });
    }
);
