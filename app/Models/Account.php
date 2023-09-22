<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function images()
    {
        return $this->hasMany(Image::class);
    }

    public function getLanguageAttribute()
    {
        return $this->settings['language'] ?? 'en';
    }
}
