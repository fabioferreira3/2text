<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;

/**
 * @codeCoverageIgnore
 */
trait UuidTrait
{
    protected static function bootUuidTrait()
    {
        static::creating(function ($model) {
            if (!$model->getKey()) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function getIncrementing()
    {
        return false;
    }

    public function getKeyType()
    {
        return 'string';
    }
}
