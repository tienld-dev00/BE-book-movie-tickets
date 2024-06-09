<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class SeatInRoomRule implements Rule
{
    protected $showtime_id;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($showtime_id)
    {
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
        // Retrieve the room_id for the given showtime
        $room_id = \DB::table('showtimes')
            ->where('id', $this->showtime_id)
            ->value('room_id');

        if (!$room_id) {
            return false;
        }

        // Check if the seat exists in the room
        $seatExists = \DB::table('seats')
            ->where('id', $value)
            ->where('room_id', $room_id)
            ->exists();

        return $seatExists;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('validation.seat_not_in_room');
    }
}
