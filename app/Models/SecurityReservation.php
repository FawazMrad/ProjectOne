<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecurityReservation extends Model
{
    use HasFactory;
    protected $fillable=[
        'security_id',
        'event_id',
        'start_date',
        'end_date',
        'guards_number',
        'cost'
    ];
       public function security(){
           return $this->belongsTo(Security::class);
       }
       public function event(){
           return $this->belongsTo(Event::class);
       }
}
