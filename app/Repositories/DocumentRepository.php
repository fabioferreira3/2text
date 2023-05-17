<?php

namespace App\Repositories;

use App\Enums\DocumentTaskEnum;
use App\Enums\Tone;
use App\Helpers\PromptHelper;
use App\Models\Document;
use App\Models\DocumentTask;
use Illuminate\Support\Str;

class DocumentRepository
{
    protected PromptHelper $promptHelper;
    protected $document;

    public function __construct(Document $document = null)
    {
        $this->document = $document;
        $this->promptHelper = new PromptHelper();
    }

    public function create(array $params): Document
    {
        return Document::create([
            ...$params,
            'meta' => [
                'title' => '',
                'context' => $params['context'] ?? '',
                'raw_structure' => [],
                'tone' => $params['meta']['tone'] ?? Tone::CASUAL->value,
                'source' => $params['source'],
                'source_url' => $params['meta']['source_url'] ?? null,
                'target_headers_count' => $params['meta']['target_headers_count'],
                'keyword' => $params['meta']['keyword'] ?? '',
            ]
        ]);
    }

    public function addHistory(array $payload, array $tokenUsage = [])
    {
        $content = is_array($payload['content']) ? json_encode($payload['content']) : $payload['content'];
        $this->document->history()->create([
            'description' => $payload['field'],
            'content' => $content,
            'word_count' => Str::wordCount($content),
            'prompt_token_usage' => $tokenUsage['prompt'] ?? 0,
            'completion_token_usage' => $tokenUsage['completion'] ?? 0,
            'total_token_usage' => $tokenUsage['total'] ?? 0,
            'model' => $tokenUsage['model'] ?? ''
        ]);
    }

    public function updateMeta($attribute, $value)
    {
        $this->document->refresh();
        $meta = $this->document->meta;
        $meta[$attribute] = $value;
        return $this->document->update(['meta' => $meta]);
    }

    public function delete($documentId)
    {
        $document = Document::findOrFail($documentId);
        return $document->delete();
    }

    public function publishText()
    {
        $content = str_replace(["\r", "\n"], '', $this->document->normalized_structure);
        $this->document->update([
            'content' => $content,
            'word_count' => Str::wordCount($content)
        ]);
    }

    public function createTask(DocumentTaskEnum $task, array $params)
    {
        DocumentTask::create([
            'name' => $task->value,
            'document_id' => $this->document->id,
            'process_id' => $params['process_id'] ?? null,
            'job' => $task->getJob(),
            'status' => $params['status'] ?? 'ready',
            'meta' => $params['meta'] ?? [],
            'order' => $params['order'] ?? 1,
        ]);
    }

    public function defineDefaultMetaVideoStream()
    {
        $this->document->refresh();
        $this->document->update([
            'meta' => [
                ...$this->document->meta,
                'title' => "",
                'original_text' => "",
                'context' => "",
                'raw_structure' => [],
                'summary' => "",
                'outline' => "",
                'meta_description' => ""
            ]
        ]);
    }
}
