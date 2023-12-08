<?php

namespace App\Enums;

enum DocumentStatus: string
{
    case ABORTED = 'aborted';
    case DRAFT = 'draft';
    case FAILED = 'failed';
    case FINISHED = 'finished';
    case IN_PROGRESS = 'in_progress';
    case ON_HOLD = 'on_hold';

    public function label()
    {
        return match ($this) {
            self::ABORTED => __('common.document.aborted'),
            self::DRAFT => __('common.document.draft'),
            self::FAILED => __('common.document.failed'),
            self::FINISHED => __('common.document.finished'),
            self::IN_PROGRESS => __('common.document.in_progress'),
            self::ON_HOLD => __('common.document.on_hold'),
        };
    }

    public static function getKeyValues(): array
    {
        return collect(self::cases())->flatMap(fn ($type) => [$type->value => $type->label()])->toArray();
    }
}
