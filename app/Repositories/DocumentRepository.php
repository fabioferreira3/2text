<?php

namespace App\Repositories;

use App\Enums\DocumentTaskEnum;
use App\Enums\DocumentType;
use App\Enums\SourceProvider;
use App\Enums\Tone;
use App\Helpers\DocumentHelper;
use App\Helpers\PromptHelper;
use App\Models\Document;
use App\Models\DocumentContentBlock;
use App\Models\DocumentTask;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DocumentRepository
{
    protected PromptHelper $promptHelper;
    protected $document;

    public function __construct(Document $document = null)
    {
        $this->setDocument($document);
        $this->promptHelper = new PromptHelper();
    }

    public function setDocument(Document $document = null)
    {
        $this->document = $document;
    }

    public function createBlogPost(array $params): Document
    {
        return Document::create([
            ...$params,
            'title' => '',
            'type' => DocumentType::BLOG_POST->value,
            'meta' => [
                'context' => $params['context'] ?? null,
                'raw_structure' => [],
                'tone' => $params['meta']['tone'] ?? Tone::CASUAL->value,
                'style' => $params['meta']['style'] ?? null,
                'source' => $params['source'],
                'source_url' => $params['meta']['source_url'] ?? null,
                'target_headers_count' => $params['meta']['target_headers_count'] ?? null,
                'keyword' => $params['meta']['keyword'] ?? null,
                'user_id' => Auth::check() ? Auth::id() : null,
            ]
        ]);
    }

    public function createSocialMediaDoc(array $params, DocumentType $type): Document
    {
        return Document::create([
            'type' => $type->value,
            'meta' => [
                'context' => $params['context'] ?? null,
                'tone' => $params['meta']['tone'] ?? Tone::CASUAL->value,
                'style' => $params['meta']['style'] ?? null,
                'source' => $params['source'],
                'source_url' => $params['meta']['source_url'] ?? null,
                'keyword' => $params['meta']['keyword'] ?? null,
                'more_instructions' => $params['meta']['more_instructions'] ?? null,
                'user_id' => Auth::check() ? Auth::id() : null,
            ]
        ]);
    }

    public function createTextToSpeech(array $params): Document
    {
        return Document::create([
            ...$params,
            'title' => '',
            'type' => DocumentType::TEXT_TO_SPEECH->value,
            'content' => $params['text'],
            'language' => $params['language'],
            'meta' => [
                'source' => SourceProvider::FREE_TEXT->value,
                'voice' => $params['voice'],
                'user_id' => Auth::check() ? Auth::id() : null,
            ]
        ]);
    }

    public function createGeneric(array $params): Document
    {
        return Document::create([
            ...$params,
            'meta' => [
                ...$params['meta'] ?? [],
                'context' => $params['context'] ?? null,
                'source' => $params['source'],
                'user_id' => Auth::check() ? Auth::id() : null,
            ]
        ]);
    }

    public function addHistory(array $payload, array $tokenUsage = [])
    {
        $content = is_array($payload['content']) ? json_encode($payload['content']) : $payload['content'];
        $this->document->history()->create([
            'description' => $payload['field'],
            'content' => $content,
            'word_count' => $payload['word_count'] ?? Str::wordCount($content),
            'char_count' => $payload['char_count'] ?? Str::wordCount($content),
            'prompt_token_usage' => $tokenUsage['prompt'] ?? 0,
            'completion_token_usage' => $tokenUsage['completion'] ?? 0,
            'total_token_usage' => $tokenUsage['total'] ?? 0,
            'audio_length' => $tokenUsage['length'] ?? 0,
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

    public function restore($documentId)
    {
        $document = Document::withTrashed()->findOrFail($documentId);
        return $document->restore();
    }

    public function publishContentBlocks()
    {
        $content = str_replace(["\r", "\n"], '', $this->document->normalized_structure);
        $blocks = DocumentHelper::parseHtmlToArray($content);
        if (count($blocks)) {
            foreach ($blocks as $key => $block) {
                $this->document->contentBlocks()->create([
                    'type' => $block['tag'],
                    'content' => $block['content'],
                    'order' => $key + 1,
                    'meta' => []
                ]);
            }
        }
        // $this->document->update([
        //     'content' => $content,
        //     'word_count' => Str::wordCount($content)
        // ]);
    }

    public static function createTask(string $documentId, DocumentTaskEnum $task, array $params)
    {
        DocumentTask::create([
            'name' => $task->value,
            'document_id' => $documentId,
            'process_id' => $params['process_id'] ?? Str::uuid(),
            'job' => $task->getJob(),
            'status' => $params['status'] ?? 'ready',
            'meta' => $params['meta'] ?? [],
            'order' => $params['order'] ?? 1,
        ]);
    }

    public static function getContentBlock(string $documentContentBlockId)
    {
        return DocumentContentBlock::findOrFail($documentContentBlockId);
    }

    public static function updateContentBlock(string $documentContentBlockId, array $params)
    {
        $contentBlock = self::getContentBlock($documentContentBlockId);
        $contentBlock->update($params);
    }
}
