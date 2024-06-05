<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class attendee extends Model
{
    use HasFactory;
    protected  $fillable=[
        'user_id',
        'event_id',
        'status',
        'checked_in',
        'purchase_date',
        'ticket_price',
        'ticket_type',
        'seat_number',
        'discount',
        'qr_code'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
