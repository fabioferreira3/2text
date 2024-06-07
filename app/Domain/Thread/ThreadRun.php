<?php

namespace App\Domain\Thread;

use App\Domain\Thread\Enum\RunStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ThreadRun extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = ['id'];
    protected $casts = [
        'meta' => 'array',
        'status' => RunStatus::class,
        'completed_at' => 'datetime',
        'failed_at' => 'datetime',
        'canceled_at' => 'datetime'
    ];

    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class);
    }
}
