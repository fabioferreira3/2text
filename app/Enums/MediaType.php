<?php

namespace App\Enums;

enum MediaType: string
{
    case IMAGE = 'image';
    case AUDIO = 'audio';

    public function label()
    {
        return match ($this) {
            self::IMAGE => __('common.image'),
            self::AUDIO => __('common.audio'),
        };
    }

    public static function getKeyValues(): array
    {
        return collect(self::cases())->flatMap(fn ($type) => [$type->value => $type->label()])->toArray();
    }
}
