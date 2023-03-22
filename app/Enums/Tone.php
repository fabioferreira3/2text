<?php

namespace App\Enums;

enum Tone: string
{
    case ACADEMIC = 'academic';
    case ADVENTUROUS = 'adventurous';
    case DRAMATIC = 'dramatic';
    case FUNNY = 'funny';
    case MYSTERIOUS = 'mysterious';
    case PESSIMISTIC = 'pessimistic';
    case SARCASTIC = 'sarcastic';

    public function labels(): string
    {
        return match ($this) {
            self::ACADEMIC => "Academic",
            self::ADVENTUROUS => "Adventurous",
            self::DRAMATIC => "Dramatic",
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
}
