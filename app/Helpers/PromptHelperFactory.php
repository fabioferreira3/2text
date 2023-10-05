<?php

namespace App\Helpers;

class PromptHelperFactory
{
    public static function create($lang = 'en')
    {
        return new DecoratedPromptHelper(new PromptHelper($lang));
    }
}
