<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Security extends Model
{
    use HasFactory;
    protected $fillable=[
        'clothes_color',
        'number_of_guards_available',
        'cost'
    ];
 public function securityReservations(){
     return $this->hasMany(SecurityReservation::class);
 }
}
