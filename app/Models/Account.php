<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    protected $guarded = ['id'];
    protected $appends = ['language'];
    protected $casts = ['settings' => 'array'];

    use HasFactory, HasUuids;

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function mediaFiles()
    {
        return $this->hasMany(MediaFile::class);
    }

    public function productUsage(): HasMany
    {
        return $this->hasMany(ProductUsage::class);
    }

    public function getLanguageAttribute()
    {
        return $this->settings['language'] ?? 'en';
    }
}
