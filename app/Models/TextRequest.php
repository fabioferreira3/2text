<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Artisan;

class TextRequest extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = ['id'];
    protected $casts = ['raw_structure' => 'array'];
    protected $appends = ['normalized_structure'];

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

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function getSummaryTokenCount()
    {
        return Artisan::call('count:token', ['string' => addslashes($this->summary)]);
    }

    public function getFinalTextTokenCount()
    {
        return Artisan::call('count:token', ['string' => addslashes($this->final_text)]);
    }
}
