<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DecorationItemReservation extends Model
{
    use HasFactory;
    protected  $fillable=[
      'venue_reservation_id',
        'decoration_item_id',
        'quantity',
        'cost'
    ];
    public function decorationItem(){
        return $this->belongsTo(DecorationItem::class);
    }
    public function venueReservationID(){
        return $this->belongsTo(VenueReservation::class);
    }
}
