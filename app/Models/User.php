<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\BelongsToManyRelationship;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasName
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

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
        'address',
        'phone_number',
        'birth_date',
        'points',
        'followers',
        'following',
        'rating',
        'profile_pic',
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
  public function getAgeAttribute(){
      return \Carbon\Carbon::parse($this->birth_date)->age;
  }
    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function attendees()
    {
        return $this->hasMany(Attendee::class);
    }

    public function preference()
    {
        return $this->hasOne(Preference::class);
    }

    public function wallet()
    {
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


    public function canAccessPanel(Panel $panel): bool
    {
        return (Auth::user()->hasRole('admin'));
    }

    public function getFilamentName(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function following()
    {
        return $this->belongsToMany(User::class, 'Friendships', 'sender_id', 'receiver_id');
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'Friendships', 'receiver_id', 'sender_id');

    }
    public function sentGifts()
    {
        return $this->hasMany(GiftHistory::class, 'sender_id');
    }

    public function receivedGifts()
    {
        return $this->hasMany(GiftHistory::class, 'receiver_id');
}
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
