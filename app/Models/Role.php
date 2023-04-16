<?php

namespace App\Models;

use App\Models\Traits\UuidTrait;
use Database\Factories\RoleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use HasFactory;
    use UuidTrait;

    protected $guarded = ['id'];
    protected $keyType = 'string';

    protected static function newFactory()
    {
        return RoleFactory::new();
    }
}
