<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'payment_method',
        'status',
        'user_id',
        'showtime_id',
    ];

    /**
     * Get the payments for the order.
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Get the tickets for the order.
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Get the amount for the order.
     */
    public function getAmount()
    {
        $amount = 0;

        foreach ($this->tickets as $ticket) {
            $amount += $ticket->price;
        }

        return $amount;
    }
}
