<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentTask extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = ['id'];
    protected $casts = ['meta' => 'array'];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function siblings()
    {
        return DocumentTask::ofProcessGroup($this->process_group_id)
            ->where('id', '!=', $this->id);
    }

    public function scopeOfProcess($query, $processId)
    {
        return $query->where('process_id', $processId);
    }

    public function scopeOfProcessGroup($query, $processGroupId)
    {
        return $query->where('process_group_id', $processGroupId);
    }

    public function scopePriorityFirst($query)
    {
        return $query->orderBy('order', 'asc');
    }

    public function scopeAvailable($query)
    {
        return $query->whereIn('status', ['ready', 'failed', 'on_hold']);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeCompleted($query)
    {
        return $query->whereIn('status', [
            'finished',
            'skipped',
            'aborted'
        ]);
    }

    public function scopeExcept($query, $taskIds)
    {
        return $query->whereNotIn('id', $taskIds);
    }
}
