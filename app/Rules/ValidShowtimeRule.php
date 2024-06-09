<?php

namespace App\Rules;

use App\Enums\ShowtimeStatus;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class ValidShowtimeRule implements Rule
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
        // Check if the start_time of the showtime is greater than the current time
        $showtime = \DB::table('showtimes')
            ->where('id', $value)
            ->where('status', ShowtimeStatus::SHOW)
            ->first();

        if ($showtime) {
            return Carbon::parse($showtime->start_time)->greaterThan(Carbon::now());
        }

        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('validation.showtime_wrong_time');
    }
}
