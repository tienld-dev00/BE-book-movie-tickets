<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'price',
        'order_id',
        'seat_id',
    ];

    /**
     * Get the seat for the ticket.
     */
    public function seat(): BelongsTo
    {
        return $this->BelongsTo(Seat::class);
    }
}
