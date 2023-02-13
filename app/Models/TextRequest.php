<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TextRequest extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = ['id'];

    public function logs(): HasMany
    {
        return $this->hasMany(TextRequestLog::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
