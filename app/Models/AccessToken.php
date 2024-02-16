<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * @codeCoverageIgnore
 */
class AccessToken extends Model
{
    protected $guarded = ['id'];

    use HasUuids;

    public function user()
    {
        return $this->hasOne(User::class);
    }
}
