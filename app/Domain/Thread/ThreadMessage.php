<?php

namespace App\Domain\Thread;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ThreadMessage extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = ['id'];
    protected $casts = [
        'content' => 'array',
        'attachments' => 'array'
    ];

    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class);
    }
}
