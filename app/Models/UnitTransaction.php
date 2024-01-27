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

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::created(function (UnitTransaction $transaction) {
            $transaction->account->update([
                'units' => $transaction->account->units + $transaction->amount
            ]);
        });
    }
}
