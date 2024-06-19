<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
     * Get the user information for the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the showtime information for the order.
     */
    public function showtime(): BelongsTo
    {
        return $this->belongsTo(Showtime::class);
    }

    /**
     * Get the payments for the order.
     */
    public function payments(): HasMany
    {
        return $this->HasMany(Payment::class);
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
     * 
     * @return number
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
