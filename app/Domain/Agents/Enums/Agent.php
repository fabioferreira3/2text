<?php

namespace App\Domain\Agents\Enums;

enum Agent: string
{
    case THE_PARAPHRASER = 'the_paraphraser';

    public function id()
    {
        return match ($this) {
            self::THE_PARAPHRASER => 'asst_QOEInQYjbIWPU6EcHAXzylBN',
        };
    }

    public function label()
    {
        return match ($this) {
            self::THE_PARAPHRASER => 'The Paraphraser',
        };
    }
}
