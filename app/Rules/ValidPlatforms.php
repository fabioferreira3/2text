<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidPlatforms implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $validKeys = ['Instagram', 'Facebook', 'Linkedin', 'Twitter'];
        if (!is_array($value) || array_diff_key(array_flip($validKeys), $value)) {
            return false;
        }

        foreach ($value as $platform => $status) {
            if (!in_array($platform, $validKeys) || !is_bool($status)) {
                return false;
            }
        }

        return in_array(true, $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'You must choose at least 1 social media platform';
    }
}
