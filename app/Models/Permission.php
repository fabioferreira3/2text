<?php

namespace App\Models;

use App\Models\Traits\UuidTrait;
use Database\Factories\PermissionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Permission as SpatiePermission;

/**
 * @codeCoverageIgnore
 */
class Permission extends SpatiePermission
{
    use UuidTrait;
    use HasFactory;

    protected $guarded = ['id'];
    protected $keyType = 'string';

    protected static function newFactory()
    {
        return PermissionFactory::new();
    }
}
