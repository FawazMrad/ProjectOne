<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DrinkReservation extends Model
{
    use HasFactory;
    protected $fillable=[
        'drink_id',
        'event_id',
        'quantity',
        'total_price',
        'serving_date'
    ];
    public function drink(){
        return $this->belongsTo(Drink::class);
    }
    public function event(){
        return $this->belongsTo(Event::class);
    }
}
