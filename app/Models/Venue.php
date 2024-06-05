<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    use HasFactory;
    protected $fillable=[
        'name',
        'location',
        'location_on_map',
        'max_capacity_no_chairs',
        'max_capacity_chairs',
        'vip_chairs',
        'is_vip',
        'website',
        'rating',
        'image',
        'cost'
    ];
public function venueReservations(){
    return $this->hasMany(VenueReservation::class);
}
}
