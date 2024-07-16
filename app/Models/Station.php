<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Station extends Model
{
    use HasFactory;
    protected $fillable=[
        'governorate',
        'name',
        'location',
        'manager_name',
        'manager_email',
        'manager_id_picture',
        'balance'
    ];
    public function fillHistories(){
        return $this->hasMany(FillHistory::class);
    }

}
