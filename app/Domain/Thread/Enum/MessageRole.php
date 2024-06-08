<?php

namespace App\Domain\Thread\Enum;

enum MessageRole: string
{
    case USER = 'user';
    case ASSISTANT = 'assistant';
}
