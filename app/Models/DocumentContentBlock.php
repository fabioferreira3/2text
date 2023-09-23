<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class DocumentContentBlock extends Model
{
    protected $guarded = ['id'];

    use HasFactory, HasUuids;

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function getUrl()
    {
        if (!$this->type === 'image') {
            return null;
        }

        return Storage::temporaryUrl($this->content, now()->addMinutes(15));
    }
}
