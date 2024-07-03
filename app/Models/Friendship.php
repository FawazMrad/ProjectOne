<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Friendship extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $table = 'friendships';
    public $timestamps = true;
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'status',
        'mutual_at',
        'blocker_id',
        'created_at'
    ];
    protected $primaryKey = ['sender_id', 'receiver_id'];

    public function getKeyName()
    {
        return ['sender_id', 'receiver_id'];
    }

    public function save(array $options = [])
    {
        // If the model exists, update it
        if ($this->exists) {
            return $this->update($this->getAttributes());
        }
        // Otherwise, perform a regular save
        return parent::save($options);
    }

    public function update(array $attributes = [], array $options = [])
    {
        // Use the composite key for updates
        $query = $this->newQueryWithoutScopes()
            ->where('sender_id', $this->sender_id)
            ->where('receiver_id', $this->receiver_id);

        return $query->update($attributes);
    }

    public static function deleteFriendship($senderId, $receiverId)
    {
        return self::where(function ($query) use ($senderId, $receiverId) {
            $query->where('sender_id', $senderId)
                ->where('receiver_id', $receiverId);
        })->orWhere(function ($query) use ($senderId, $receiverId) {
            $query->where('sender_id', $receiverId)
                ->where('receiver_id', $senderId);
        })->delete();
    }
    public function blocker()
    {
        return $this->belongsTo(User::class, 'blocker_id');
    }
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function sentFriendRequests()
    {
        return $this->hasMany(Friendship::class, 'sender_id');
    }

    public function receivedFriendRequests()
    {
        return $this->hasMany(Friendship::class, 'receiver_id');
    }
}
