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
use App\Models\ProductUsage;
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
                'img_prompt' => $params['meta']['img_prompt'] ?? null,
                'keyword' => $params['meta']['keyword'] ?? null,
                'generate_image' => $params['meta']['generate_image'] ?? false,
                'raw_structure' => [],
                'style' => $params['meta']['style'] ?? null,
                'source_file_path' => $params['meta']['source_file_path'] ?? null,
                'source' => $params['source'],
                'source_urls' => $params['meta']['source_urls'] ?? [],
                'target_headers_count' => $params['meta']['target_headers_count'] ?? null,
                'tone' => $params['meta']['tone'] ?? Tone::CASUAL->value,
                'user_id' => Auth::check() ? Auth::id() : null,
            ]
        ]);
    }

    public function createSocialMediaDoc(array $params, DocumentType $type): Document
    {
        return Document::create([
            'type' => $type->value,
            'language' => $params['language'],
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

    public static function createTextToAudio(array $params = []): Document
    {
        return Document::create([
            ...$params,
            'title' => '',
            'language' => 'en',
            'type' => DocumentType::TEXT_TO_SPEECH->value,
            'content' => $params['input_text'] ?? '',
            'meta' => [
                'source' => SourceProvider::FREE_TEXT->value,
                'voice_id' => $params['voice_id'],
                'user_id' => Auth::check() ? Auth::id() : null,
            ]
        ]);
    }

    public static function createGeneric(array $params): Document
    {
        return Document::create([
            ...$params,
            'meta' => [
                ...$params['meta'] ?? [],
                'context' => $params['context'] ?? null,
                'source' => $params['source'] ?? null,
                'user_id' => Auth::check() ? Auth::id() : null,
            ]
        ]);
    }

    public function updateMeta($attribute, $value)
    {
        $this->document->refresh();
        $meta = $this->document->meta;
        $meta[$attribute] = $value;

        return $this->document->update(['meta' => $meta]);
    }

    public function updateTask(string $taskId, $status)
    {
        $documentTask = DocumentTask::findOrFail($taskId);
        return $documentTask->update(['status' => $status]);
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

        $this->document->update([
            'word_count' => Str::wordCount($content)
        ]);
    }

    public function increaseCompletedTasksCount()
    {
        $this->document->refresh();
        $completedTasksCount = $this->document->getMeta('completed_tasks_count') ?? 0;
        $completedTasksCount += 1;
        $meta = $this->document->meta;
        $meta['completed_tasks_count'] = $completedTasksCount;
        $this->document->update(['meta' => $meta]);

        return $completedTasksCount;
    }

    public static function createTask(string $documentId, DocumentTaskEnum $task, array $params)
    {
        DocumentTask::create([
            'name' => $task->value,
            'document_id' => $documentId,
            'process_group_id' => $params['process_group_id'] ?? null,
            'process_id' => $params['process_id'] ?? Str::uuid(),
            'job' => $task->getJob(),
            'status' => $params['status'] ?? 'ready',
            'meta' => $params['meta'] ?? [],
            'order' => $params['order'] ?? 1,
        ]);
    }

    public function getContentBlock(string $documentContentBlockId)
    {
        return DocumentContentBlock::findOrFail($documentContentBlockId);
    }

    public function updateContentBlock(string $documentContentBlockId, array $params)
    {
        $contentBlock = $this->getContentBlock($documentContentBlockId);
        $contentBlock->update($params);
    }

    public static function clearContentBlocks(Document $document)
    {
        return $document->contentBlocks()->delete();
    }

    public static function getProductUsage(Document $document)
    {
        return ProductUsage::where('account_id', $document->account_id)
            ->where('meta->document_id', $document->id)->get();
    }

    public static function getProductUsageCosts(Document $document)
    {
        $productUsage = self::getProductUsage($document);
        return $productUsage->reduce(function ($carry, $usage) {
            return $carry + $usage->cost;
        }, 0);
    }
}
