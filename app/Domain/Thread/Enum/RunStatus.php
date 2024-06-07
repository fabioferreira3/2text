<?php

namespace App\Domain\Thread\Enum;

enum RunStatus: string
{
    case CANCELLING = 'cancelling';
    case CANCELLED = 'cancelled';
    case COMPLETED = 'completed';
    case EXPIRED = 'expired';
    case FAILED = 'failed';
    case IN_PROGRESS = 'in_progress';
    case INCOMPLETE = 'incomplete';
    case QUEUED = 'queued';
    case REQUIRES_ACTION = 'requires_action';
}
