<?php

namespace App\Models;

use App\Enums\DocumentType;
use App\Enums\Language;
use App\Models\Scopes\SameAccountScope;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Document extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $guarded = ['id'];
    protected $casts = ['type' => DocumentType::class, 'language' => Language::class, 'meta' => 'array'];
    protected $appends = ['normalized_structure', 'context', 'is_completed'];

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

    public function getIsCompletedAttribute()
    {
        $finishedCount = $this->tasks->whereIn('status', ['finished', 'skipped'])->count();
        if ($finishedCount === 0) {
            return false;
        }
        return $this->tasks->count() === $finishedCount;
    }

    public function getContextAttribute()
    {
        return $this->meta['summary'] ?? $this->meta['context'] ?? '';
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
