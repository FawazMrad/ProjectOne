<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Furniture extends Model
{
    use HasFactory;
    protected $fillable=[
      'name',
      'type',
      'quantity',
      'image',
        'cost'
    ];
    public function furnitureReservations(){
        return $this->hasMany(FurnitureReservation::class);
    }

}
