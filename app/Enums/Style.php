<?php

namespace App\Enums;

use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use InvalidArgumentException;

enum Style: string
{
    case DESCRIPTIVE = 'descriptive';
    case EXPOSITORY = 'expository';
    case NARRATIVE = 'narrative';
    case PERSUASIVE = 'persuasive';

    public function labels($language = 'en'): string
    {
        return match ($this) {
            self::DESCRIPTIVE => $this->parseLabel('descriptive', $language),
            self::EXPOSITORY => $this->parseLabel('expository', $language),
            self::NARRATIVE => $this->parseLabel('narrative', $language),
            self::PERSUASIVE => $this->parseLabel('persuasive', $language),
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

    private function parseLabel($styleValue, $language = 'en')
    {
        return Str::ucfirst(Lang::get('styles.' . $styleValue, [], $language));
    }
}
