<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class YouTubeUrl implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return preg_match('#^https?://(?:www\.)?youtube\.com/watch\?v=[A-Za-z0-9_-]+$#', $value)
            || preg_match('#^https?://(?:www\.)?youtu\.be/[A-Za-z0-9_-]+$#', $value);
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
