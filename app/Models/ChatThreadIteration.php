<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatThreadIteration extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = ['id'];

    public function thread(): BelongsTo
    {
        return $this->belongsTo(ChatThread::class);
    }
}
