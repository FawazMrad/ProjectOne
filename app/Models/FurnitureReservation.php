<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FurnitureReservation extends Model
{
    use HasFactory;
    protected $fillable=[
      'event_id',
      'furniture_id',
      'quantity',
      'start_date',
      'end_date',
      'cost'
    ];
    public function furniture(){
        return $this->belongsTo(Furniture::class);
    }
    public function event(){
        return $this->belongsTo(Event::class);

    }
}
