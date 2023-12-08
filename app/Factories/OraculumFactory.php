<?php

namespace App\Factories;

use App\Interfaces\OraculumFactoryInterface;
use App\Models\User;
use App\Packages\Oraculum\Oraculum;

class OraculumFactory implements OraculumFactoryInterface
{
    public function make(User $user, string $collectionName): Oraculum
    {
        return new Oraculum($user, $collectionName);
    }
}
