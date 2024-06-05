<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoundReservation extends Model
{
    use HasFactory;
    protected $fillable=[
        'event_id',
        'sound_id',
        'start_date',
        'end_date',
        'cost'
    ];
    public function sound(){
        return $this->belongsTo(Sound::class);
    }
    public function event(){
        return $this->belongsTo(Event::class);
    }
}
