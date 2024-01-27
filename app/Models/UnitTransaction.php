<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnitTransaction extends Model
{
    use HasUuids, HasFactory;

    protected $guarded = ['id'];
    protected $casts = ['meta' => 'array'];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
