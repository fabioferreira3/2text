<?php

namespace App\Models;

use App\Enums\DocumentStatus;
use App\Enums\DocumentType;
use App\Enums\Language;
use App\Enums\SourceProvider;
use App\Enums\Style;
use App\Enums\Tone;
use App\Models\Scopes\SameAccountScope;
use App\Models\Traits\TimezoneHandler;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Document extends Model
{
    use HasFactory, HasUuids, SoftDeletes, TimezoneHandler;

    protected $guarded = ['id'];
    protected $casts = [
        'type' => DocumentType::class,
        'language' => Language::class,
        'meta' => 'array'
    ];
    protected $appends = ['normalized_structure', 'is_finished', 'status', 'source', 'tone', 'style'];

    public function history(): HasMany
    {
        return $this->hasMany(DocumentHistory::class)->latest();
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(DocumentTask::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function parent()
    {
        return $this->belongsTo(Document::class, 'parent_document_id');
    }

    public function children()
    {
        return $this->hasMany(Document::class, 'parent_document_id');
    }

    public function contentBlocks(): HasMany
    {
        return $this->hasMany(DocumentContentBlock::class);
    }

    public function getRawStructureDescription()
    {
        $details = "";

        if (!isset($this->meta['raw_structure'])) {
            return $details;
        }

        foreach ($this->meta['raw_structure'] as $topic) {
            $details .= "Topic: " . $topic['subheader'] . "\n" .
                "Context: " . $topic['content'] . "\n\n";
        }

        return $details;
    }

    public function getMeta($attribute)
    {
        return $this->meta[$attribute] ?? $this->parent->meta[$attribute] ?? null;
    }

    public function getContext()
    {
        return $this->getMeta('summary') ?? $this->getMeta('context') ?? null;
    }

    public function getLatestImages($amount)
    {
        return MediaFile::where('meta->document_id', $this->id)->take($amount)->latest()->get();
    }

    public function getLatestAudios()
    {
        return MediaFile::where('meta->document_id', $this->id)->where('type', 'audio')->latest()->get();
    }

    public function getTextContentBlocksAsText()
    {
        return $this->contentBlocks()->ofTextType()->get()->reduce(function ($carry, $block) {
            return $carry . $block->content;
        }, '');
    }

    public function getHtmlContentBlocksAsText()
    {
        return $this->contentBlocks->reduce(function ($carry, $block) {
            return $carry . "<" . $block->type . ">" . $block->content . "</" . $block->type . ">";
        }, '');
    }

    public function getContentBlockOfType($type)
    {
        return $this->contentBlocks()->where('type', $type)->first();
    }

    public function getNormalizedStructureAttribute()
    {
        $text = '';
        if (!isset($this->meta['raw_structure'])) {
            return $text;
        }

        collect($this->meta['raw_structure'])->each(function ($section) use (&$text) {
            $text .= "<h2>" . $section['subheader'] . "</h2>";
            $text .= collect($section['content'])->map(function ($item) {
                return $item;
            })->implode('');
        });

        return $text;
    }

    public function getParaphrasedTextAttribute()
    {
        if ($this->type === DocumentType::PARAPHRASED_TEXT && ($this->meta['paraphrased_sentences'] ?? false)) {
            return collect($this->meta['paraphrased_sentences'])->sortBy('sentence_order')->map(function ($sentence) {
                return $sentence['text'];
            })->implode(' ');
        }

        return null;
    }

    public function getIsFinishedAttribute()
    {
        return $this->status === DocumentStatus::FINISHED;
    }

    public function getStatusAttribute()
    {
        $abortedCount = $this->tasks->whereIn('status', ['aborted'])->count();
        if ($abortedCount !== 0) {
            return DocumentStatus::ABORTED;
        }

        $failedCount = $this->tasks->whereIn('status', ['failed'])->count();
        if ($failedCount !== 0) {
            return DocumentStatus::FAILED;
        }

        $mainTasksinProgressCount = $this->tasks->whereIn('status', ['in_progress', 'pending', 'on_hold'])->count();
        $childTasksInProgressCount = $this->children->reduce(function ($carry, $child) {
            return $carry + $child->tasks->whereIn('status', ['in_progress', 'pending', 'on_hold'])->count();
        }, 0);

        if (($mainTasksinProgressCount + $childTasksInProgressCount) > 0) {
            return DocumentStatus::IN_PROGRESS;
        }

        $finishedCount = $this->tasks->whereIn('status', ['finished', 'skipped'])->count();

        if ($this->tasks->count() === $finishedCount && $finishedCount > 0) {
            return DocumentStatus::FINISHED;
        }

        if ($this->tasks->count()) {
            return DocumentStatus::ON_HOLD;
        }

        return DocumentStatus::DRAFT;
    }

    public function getSourceAttribute()
    {
        if (!isset($this->meta['source'])) {
            return null;
        }

        $source = SourceProvider::tryFrom($this->meta['source']);
        return $source->label();
    }

    public function getToneAttribute()
    {
        if (!isset($this->meta['tone'])) {
            return null;
        }

        $tone = Tone::tryFrom($this->meta['tone']);
        return $tone->label();
    }

    public function getStyleAttribute()
    {
        if (!isset($this->meta['style'])) {
            return null;
        }

        $style = Style::tryFrom($this->meta['style']);
        return $style->label();
    }

    public function flushWordCount()
    {
        $wordCount = Str::wordCount($this->getTextContentBlocksAsText());
        $this->update(['word_count' => $wordCount]);
    }

    public function getYoutubeVideoId()
    {
        if (($this->getMeta('source_url') && $this->type === DocumentType::AUDIO_TRANSCRIPTION) || $this->getMeta('source') === SourceProvider::YOUTUBE->value) {
            preg_match('/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $this->getMeta('source_url'), $matches);
            return $matches[1] ?? null;
        }

        return null;
    }

    public function scopeOfMediaPosts($query)
    {
        return $query->where('type', DocumentType::SOCIAL_MEDIA_POST);
    }

    public function scopeOfTextToSpeech($query)
    {
        return $query->where('type', DocumentType::TEXT_TO_SPEECH);
    }

    public function recalculateWordCount()
    {
        $wordCount = 0;
        if ($this->contentBlocks()->ofTextType()->get()->count()) {
            $wordCount = $this->contentBlocks()->ofTextType()->get()->reduce(function ($carry, $block) {
                return $carry + Str::wordCount($block->latest_content);
            }, 0);
        }

        $this->update(['word_count' => $wordCount]);
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new SameAccountScope());
        static::saving(function ($document) {
            if (Auth::check() && Auth::user()->account_id) {
                $document->account_id = Auth::user()->account_id;
            }
        });
    }
}
