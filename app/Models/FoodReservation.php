<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodReservation extends Model
{
    use HasFactory;
    protected $fillable=[
      'food_id',
      'event_id',
      'quantity',
      'total_price',
      'serving_date'
    ];
    public function food(){
        return $this->belongsTo(Food::class);
    }
    public function event(){
        return $this->belongsTo(Event::class);
    }
}
