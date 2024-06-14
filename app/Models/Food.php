<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    use HasFactory;
    protected $fillable=[
      'name',
      'type',
      'description_en',
      'description_ar',
      'cost',
      'image'
    ];
    public function foodReservations(){
        return $this->hasMany(FoodReservation::class);
    }
}
