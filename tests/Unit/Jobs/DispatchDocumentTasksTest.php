<?php

use App\Enums\DocumentTaskEnum;
use App\Jobs\DispatchDocumentTasks;
use App\Jobs\ExpandTextSection;
use App\Models\Document;
use App\Models\DocumentTask;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->processId = Str::uuid();
    $this->processGroupId = Str::uuid();
    $this->document = Document::factory()->create();
    $this->job = new DispatchDocumentTasks($this->document);
});

it('dispatches document tasks', function ($readyStatus) {
    for ($x = 1; $x < 4; $x++) {
        DocumentTask::factory()->create([
            'document_id' => $this->document,
            'order' => $x,
            'status' => $readyStatus,
            'process_id' => $this->processId,
            'job' => DocumentTaskEnum::EXPAND_TEXT_SECTION->getJob(),
        ]);
    }

    $tasks = DocumentTask::orderBy('order', 'ASC')->get();
    $this->job->handle();

    Bus::assertChained([
        new ExpandTextSection($this->document, [
            'task_id' => $tasks[0]->id,
            'process_id' => (string) $this->processId,
            'process_group_id' => null,
            'order' => 1,
        ]),
        new ExpandTextSection($this->document, [
            'task_id' => $tasks[1]->id,
            'process_id' => (string) $this->processId,
            'process_group_id' => null,
            'order' => 2,
        ]),
        new ExpandTextSection($this->document, [
            'task_id' => $tasks[2]->id,
            'process_id' => (string) $this->processId,
            'process_group_id' => null,
            'order' => 3,
        ]),
    ]);

    foreach ($tasks as $task) {
        $task->refresh();
        expect($task->status)->toBe('in_progress');
    }
})->with(['ready', 'failed', 'on_hold']);

it('does not dispatch tasks if there are no available tasks', function ($notReadyStatus) {
    for ($x = 1; $x < 4; $x++) {
        DocumentTask::factory()->create([
            'document_id' => $this->document,
            'order' => $x,
            'status' => $notReadyStatus,
            'process_id' => $this->processId,
            'job' => DocumentTaskEnum::EXPAND_TEXT_SECTION->getJob(),
        ]);
    }
    $this->job->handle();
    Bus::assertNotDispatched(ExpandTextSection::class);
})->with(['in_progress', 'finished', 'aborted']);
