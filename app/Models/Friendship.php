<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Friendship extends Model
{
    use HasFactory;
    protected $fillable=[
      'sender_id',
        'receiver_id',
        'status',
        'requested_at',
        'accepted_at',

    ];
    public function sender()
    {
        return $this->belongsTo(User::class,'sender_id');
    }
    public function receiver()
    {
        return $this->belongsTo(User::class,'receiver_id');
    }
    public function sentFriendRequests()
    {
        return $this->hasMany(Friendship::class,'sender_id');
    }

    public function receivedFriendRequests()
    {
        return $this->hasMany(Friendship::class,'receiver_id');
    }
}
