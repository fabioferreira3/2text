<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Voice extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['meta' => 'array'];

    use HasUuids;
}
