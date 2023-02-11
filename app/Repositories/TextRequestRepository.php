<?php

namespace App\Repositories;

use App\Models\TextRequest;

class TextRequestRepository
{
    public static function create(array $params): TextRequest
    {
        return TextRequest::create($params);
    }
}
