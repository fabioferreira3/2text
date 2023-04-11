<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class TextRequest extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = ['id'];
    protected $casts = ['raw_structure' => 'array'];
    protected $appends = [
        'context',
        'normalized_structure',
        'total_costs',
        'original_text_word_count',
    ];

    public function logs(): HasMany
    {
        return $this->hasMany(TextRequestLog::class);
    }

    public function getNormalizedStructureAttribute()
    {
        $text = '';
        collect($this->raw_structure)->each(function ($section) use (&$text) {
            $text .= "<h2>" . $section['subheader'] . "</h2>";
            $text .= collect($section['content'])->map(function ($item) {
                return $item;
            })->implode('');
        });

        return $text;
    }

    public function getTotalCostsAttribute()
    {
        return $this->logs()->sum('costs');
    }

    public function getCostPerWord()
    {
        return number_format($this->total_costs / $this->word_count, 4);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function getContextAttribute()
    {
        return $this->summary ? $this->summary : $this->original_text;
    }

    public function getOriginalTextWordCountAttribute()
    {
        return Str::wordCount($this->original_text);
    }
}
