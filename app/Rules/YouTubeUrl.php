<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class YouTubeUrl implements ValidationRule
{

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!(preg_match('#^https?://(?:www\.)?youtube\.com/watch\?v=[A-Za-z0-9_-]+(&.*)?$#', $value) ||
            preg_match('#^https?://(?:www\.)?youtu\.be/[A-Za-z0-9_-]+(\?.*)?$#', $value))) {
            $fail($this->message());
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The provided URL is not a valid YouTube video URL.';
    }
}
