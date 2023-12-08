<?php

namespace App\Interfaces;

use App\Models\User;
use App\Packages\Oraculum\Oraculum;

interface OraculumFactoryInterface
{
    public function make(User $user, string $collectionName): Oraculum;
}
