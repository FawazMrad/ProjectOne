<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'google_id',
        'address',
        'phone_number',
        'age',
        'points',
        'rating',
        'profile_pic'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function attendees()
    {
        return $this->hasMany(Attendee::class);
    }
    public function preference(){
        return $this->hasOne(Preference::class);
    }
    public function wallet(){
        return $this->hasOne(wallet::class);
    }
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }
    public function favourites()
    {
        return $this->hasMany(Favourite::class);
    }
}
