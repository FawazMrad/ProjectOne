<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;
    protected $fillable=[
        'user_id',
        'category_id',
        'title',
        'description_ar',
        'description_en',
        'start_date',
        'end_date',
        'min_age',
        'is_paid',
        'is_private',
        'attendance_type',
        'total_cost',
        'ticket_price',
        'image',
        'qr_code'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function attendees()
    {
        return $this->hasMany(Attendee::class);
    }
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }
    public function favourites()
    {
        return $this->hasMany(Favourite::class);
    }
    public function securityReservations(){
        return $this->hasMany(SecurityReservation::class);
    }
    public function soundReservations(){
        return $this->hasMany(SoundReservation::class);
    }
    public function venueReservations(){
        return $this->hasMany(VenueReservation::class);
    }
    public function furnitureReservations(){
        return $this->hasMany(FurnitureReservation::class);
    }
    public function foodReservations(){
        return $this->hasMany(FoodReservation::class);
    }
    public function drinkReservations()
    {
        return $this->hasMany(DrinkReservation::class);
    }
}
