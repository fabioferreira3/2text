<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class DocumentContentBlockVersion extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = ['id'];

    public function contentBlock(): BelongsTo
    {
        return $this->belongsTo(DocumentContentBlock::class);
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope('versioned', function (Builder $builder) {
            $builder->orderBy('version', 'DESC');
        });
    }
}
