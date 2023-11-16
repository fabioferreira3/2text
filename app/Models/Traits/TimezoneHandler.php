<?php

namespace App\Models\Traits;

trait TimezoneHandler
{
    /**
     * Convert the created_at attribute to the account's timezone.
     *
     * @param  string  $value
     * @return \Carbon\Carbon
     */
    public function getCreatedAtAttribute($value)
    {
        return $this->convertToAccountTimezone($value);
    }

    /**
     * Convert the updated_at attribute to the account's timezone.
     *
     * @param  string  $value
     * @return \Carbon\Carbon
     */
    public function getUpdatedAtAttribute($value)
    {
        return $this->convertToAccountTimezone($value);
    }

    /**
     * Convert a date to the account's timezone.
     *
     * @param  string  $value
     * @return \Carbon\Carbon
     */
    protected function convertToAccountTimezone($value)
    {
        return \Carbon\Carbon::parse($value)->timezone(session('user_timezone'));
    }
}
