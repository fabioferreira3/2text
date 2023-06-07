<?php

namespace App\Enums;

use Illuminate\Support\Facades\Lang;
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

    public function label(): string
    {
        return match ($this) {
            self::ACADEMIC => __('tones.academic'),
            self::ADVENTUROUS => __('tones.adventurous'),
            self::CASUAL => __('tones.casual'),
            self::DRAMATIC => __('tones.dramatic'),
            self::FORMAL => __('tones.formal'),
            self::FUNNY => __('tones.funny'),
            self::MYSTERIOUS => __('tones.mysterious'),
            self::PESSIMISTIC => __('tones.pessimistic'),
            self::OPTIMISTIC => __('tones.optimistic'),
            self::SARCASTIC => __('tones.sarcastic'),
            self::SIMPLISTIC => __('tones.simplistic'),
        };
    }

    public static function fromLanguage($tone, $language = 'en')
    {
        $enumTone = self::tryFrom($tone);

        if (!$enumTone) {
            throw new InvalidArgumentException('Invalid tone provided: ' . $tone);
        }

        return Lang::get('tones.' . $enumTone->value, [], $language);
    }
}
