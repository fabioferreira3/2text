<?php

namespace App\Models;

use App\Enums\DocumentType;
use App\Enums\Language;
use App\Models\Scopes\SameAccountScope;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Document extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = ['id'];
    protected $casts = ['type' => DocumentType::class, 'language' => Language::class];

    public function history(): HasMany
    {
        return $this->hasMany(DocumentHistory::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(DocumentTask::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new SameAccountScope());
        static::saving(function ($document) {
            if (Auth::check() && Auth::user()->account_id) {
                $document->account_id = Auth::user()->account_id;
            }
        });
    }
}
