<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ShortLink extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = ['id'];
    protected $casts = ['expires_at' => 'datetime'];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function scopeValid($query)
    {
        return $query->where(function ($subquery) {
            return $subquery->where('expires_at', '>', now())->orWhere('expires_at', null);
        });
    }

    protected static function booted(): void
    {
        static::saving(function ($shortLink) {
            $shortLink->link = Str::uuid();
            if (Auth::check() && Auth::user()->account_id) {
                $shortLink->account_id = Auth::user()->account_id;
            }
        });
    }
}
