<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sound extends Model
{
    use HasFactory;
    protected $fillable=[
      'type',
      'genre',
      'artist',
      'rating',
      'image',
      'cost'
    ];
    public function soundReservations(){
        return $this->hasMany(SoundReservation::class);
    }
}
