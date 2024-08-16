<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;
    protected $fillable=[
      'event_id',
      'user_id',
      'comment',
      'venue_rating',
        'decor_rating',
        'music_rating',
        'food_rating',
        'drink_rating',
        'aggregate_rating'
    ];
    public function event()
    {
        return $this->belongsTo(Event::class);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function getKey()
    {
        return $this->event_id . '-' . $this->user_id;
    }
}
