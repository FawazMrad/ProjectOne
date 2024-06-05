<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VenueReservation extends Model
{
    use HasFactory;
   protected $fillable=[
     'venue_id',
     'event_id',
     'start_date',
     'end_date',
       'booked_seats',
       'cost'
   ];
   public function venue(){
       return $this->belongsTo(Venue::class);
   }
   public function event(){
       return $this->belongsTo(Event::class);
   }
   public function decorationItemReservations(){
       return $this->hasMany(DecorationItemReservation::class);
   }
}
