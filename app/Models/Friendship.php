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
        return $this->belongsTo(User::class);
    }
    public function reciver()
    {
        return $this->belongsTo(User::class);
    }
    public function sentFriendRequests()
    {
        return $this->hasMany(Friendship::class, 'requester_id');
    }

    public function receivedFriendRequests()
    {
        return $this->hasMany(Friendship::class, 'requested_id');
    }
}
