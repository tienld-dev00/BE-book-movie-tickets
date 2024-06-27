<?php

namespace App\Rules;

use App\Models\Showtime;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Carbon;

class ShowtimeOverlapRule implements Rule
{
    protected $start_time;
    protected $end_time;
    protected $room_id;
    protected $showtime_id;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($start_time, $end_time, $room_id, $showtime_id = null)
    {
        $this->start_time = Carbon::parse($start_time);
        $this->end_time = Carbon::parse($end_time);
        $this->room_id = $room_id;
        $this->showtime_id = $showtime_id;
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
        return !Showtime::where('room_id', $this->room_id)
            ->where('id', '!=', $this->showtime_id)
            ->where('start_time', '<', $this->end_time)
            ->where('end_time', '>', $this->start_time)
            ->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('validation.showtime_overlap');
    }
}
