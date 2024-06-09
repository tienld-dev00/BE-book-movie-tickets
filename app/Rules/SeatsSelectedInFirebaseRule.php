<?php

namespace App\Rules;

use App\Services\Firebase\FireStoreService;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class SeatsSelectedInFirebaseRule implements Rule
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
    public function passes($attribute, $seats)
    {
        $firestore = FirestoreService::connect();

        $collectionReference = $firestore->collection('seats');
        $data = $collectionReference
            ->where('user_id', '=', Auth::id())
            ->where('showtime_id', '=', $this->showtime_id)
            ->where('status', '=', false) /** Haven't ordered yet */
            ->documents();

        foreach ($data as $value) {
            if (!in_array($value->data()['id'], $seats))
                return false;
        }

        return count($seats) === $data->size();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('validation.seat_not_in_firestore');
    }
}
