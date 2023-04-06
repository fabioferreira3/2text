<?php

namespace App\Enums;

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
    case PESSIMISTIC = 'pessimistic';
    case SARCASTIC = 'sarcastic';

    public function labels(): string
    {
        return match ($this) {
            self::ACADEMIC => "Academic",
            self::ADVENTUROUS => "Adventurous",
            self::CASUAL => "Casual",
            self::DRAMATIC => "Dramatic",
            self::FORMAL => "Formal",
            self::FUNNY => "Funny",
            self::MYSTERIOUS => "Mysterious",
            self::PESSIMISTIC => "Pessimistic",
            self::SARCASTIC => "Sarcastic",
        };
    }

    public function labelPowergridFilter(): string
    {
        return $this->labels();
    }

    public static function fromLanguage($tone, $language)
    {
        $enumTone = self::tryFrom($tone);

        if (!$enumTone) {
            throw new InvalidArgumentException('Invalid tone provided');
        }

        return match ($language) {
            'en' => $enumTone->value,
            'pt' => self::translateToPortuguese($enumTone),
            default => $enumTone->value,
        };
    }

    private static function translateToPortuguese(Tone $tone): string
    {
        return match ($tone) {
            self::ACADEMIC => "Acadêmico",
            self::ADVENTUROUS => "Aventureiro",
            self::CASUAL => "Casual",
            self::DRAMATIC => "Dramático",
            self::FORMAL => "Formal",
            self::FUNNY => "Engraçado",
            self::MYSTERIOUS => "Misterioso",
            self::PESSIMISTIC => "Pessimista",
            self::SARCASTIC => "Sarcástico",
            default => $tone->value,
        };
    }
}
