<?php

namespace App\Rules;

use App\Enums\OrderStatus;
use Illuminate\Contracts\Validation\Rule;

class SeatNotBookedRule implements Rule
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
     * @param  mixed  $seatId
     * @return bool
     */
    public function passes($attribute, $seatId)
    {
        // Check if the seat is already booked for the given showtime
        $seatBooked = \DB::table('tickets')
            ->join('orders', 'tickets.order_id', '=', 'orders.id')
            ->where('orders.showtime_id', $this->showtime_id)
            ->where('tickets.seat_id', $seatId)
            ->where('orders.status', OrderStatus::PAYMENT_SUCCEEDED)
            ->exists();

        return !$seatBooked;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('validation.seat_booked');
    }
}
