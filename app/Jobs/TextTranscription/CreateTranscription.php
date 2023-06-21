<?php

namespace App\Jobs\TextTranscription;

use App\Enums\DocumentTaskEnum;
use App\Enums\DocumentType;
use App\Enums\Language;
use App\Jobs\DispatchDocumentTasks;
use App\Repositories\DocumentRepository;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class CreateTranscription
{
    use Dispatchable, SerializesModels;

    protected $repo;
    protected array $params;

    public function __construct(array $params)
    {
        $this->params = [
            ...$params,
            'process_id' => Str::uuid(),
            'type' => DocumentType::TEXT_TRANSCRIPTION->value
        ];
        $this->repo = new DocumentRepository();
    }

    public function handle()
    {
        $document = $this->repo->createGeneric($this->params);
        $this->repo->setDocument($document);
        $this->repo->createTask(
            DocumentTaskEnum::DOWNLOAD_AUDIO,
            [
                'meta' => [
                    'source_url' => $document['meta']['source_url']
                ],
                'order' => 1
            ]
        );
        $this->repo->createTask(DocumentTaskEnum::PROCESS_AUDIO, ['order' => 2]);
        if ($this->params['target_language'] !== 'same') {
            $this->repo->createTask(DocumentTaskEnum::TRANSLATE_TEXT, ['order' => 3, 'meta' => [
                'target_language' => Language::from($this->params['target_language'])->name
            ]]);
        }
        $this->repo->createTask(DocumentTaskEnum::PUBLISH_TRANSCRIPTION, ['order' => 4]);
        DispatchDocumentTasks::dispatch($document);
    }
}
