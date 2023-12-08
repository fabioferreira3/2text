<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class DocumentContentBlockVersion extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = ['id'];

    public function contentBlock(): BelongsTo
    {
        return $this->belongsTo(DocumentContentBlock::class);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
