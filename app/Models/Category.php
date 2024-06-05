<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable=[
        'name',
        'description_en',
        'description_ar',
        'icon'
    ];
    public function events(){
        return $this->hasMany(Event::class);
    }
}
