<?php

namespace App\Models;

use App\Enums\MediaType;
use App\Models\Scopes\SameAccountScope;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MediaFile extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['meta' => 'array', 'type' => MediaType::class];

    use HasFactory, HasUuids;

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function getUrl()
    {
        return Storage::temporaryUrl($this->file_name, now()->addMinutes(15));
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
