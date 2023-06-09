<?php

namespace App\Enums;

use Illuminate\Support\Facades\Lang;
use InvalidArgumentException;

enum Style: string
{
    case DESCRIPTIVE = 'descriptive';
    case EXPOSITORY = 'expository';
    case NARRATIVE = 'narrative';
    case PERSUASIVE = 'persuasive';

    public function label(): string
    {
        return match ($this) {
            self::DESCRIPTIVE => __('styles.descriptive'),
            self::EXPOSITORY => __('styles.expository'),
            self::NARRATIVE => __('styles.narrative'),
            self::PERSUASIVE => __('styles.persuasive'),
        };
    }

    public static function fromLanguage($style, $language = 'en')
    {
        $enumStyle = self::tryFrom($style);

        if (!$enumStyle) {
            throw new InvalidArgumentException('Invalid style provided');
        }

        return Lang::get('styles.' . $enumStyle->value, [], $language);
    }
}
