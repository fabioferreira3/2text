<?php

namespace App\Enums;

use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use InvalidArgumentException;

enum Tone: string
{
    case ACADEMIC = 'academic';
    case ADVENTUROUS = 'adventurous';
    case CASUAL = 'casual';
    case DRAMATIC = 'dramatic';
    case FORMAL = 'formal';
    case FUNNY = 'funny';
    case MYSTERIOUS = 'mysterious';
    case OPTIMISTIC = 'optimistic';
    case PESSIMISTIC = 'pessimistic';
    case SARCASTIC = 'sarcastic';
    case SIMPLISTIC = 'simplistic';

    public function labels($language = 'en'): string
    {
        return match ($this) {
            self::ACADEMIC => $this->parseLabel('academic', $language),
            self::ADVENTUROUS => $this->parseLabel('adventurous', $language),
            self::CASUAL => $this->parseLabel('casual', $language),
            self::DRAMATIC => $this->parseLabel('dramatic', $language),
            self::FORMAL => $this->parseLabel('formal', $language),
            self::FUNNY => $this->parseLabel('funny', $language),
            self::MYSTERIOUS => $this->parseLabel('mysterious', $language),
            self::PESSIMISTIC => $this->parseLabel('pessimistic', $language),
            self::OPTIMISTIC => $this->parseLabel('optimistic', $language),
            self::SARCASTIC => $this->parseLabel('sarcastic', $language),
            self::SIMPLISTIC => $this->parseLabel('simplistic', $language),
        };
    }

    public static function fromLanguage($tone, $language = 'en')
    {
        $enumTone = self::tryFrom($tone);

        if (!$enumTone) {
            throw new InvalidArgumentException('Invalid tone provided');
        }

        return Lang::get('tones.' . $enumTone->value, [], $language);
    }

    private function parseLabel($toneValue, $language = 'en')
    {
        return Str::ucfirst(Lang::get('tones.' . $toneValue, [], $language));
    }
}
