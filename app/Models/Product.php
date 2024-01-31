<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasUuids, HasFactory;

    protected $guarded = ['id'];
    protected $casts = ['meta' => 'array'];

    public function scopeLevelOrdered($query)
    {
        return $query->orderBy('level', 'asc');
    }

    public function scopeOfExternalId($query, $externalId)
    {
        return $query->where('external_id', $externalId);
    }
}
