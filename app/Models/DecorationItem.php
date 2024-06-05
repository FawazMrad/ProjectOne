<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DecorationItem extends Model
{
    use HasFactory;
    protected $fillable=[
        'category_id',
        'name',
        'image',
        'description_en',
        'description_ar',
        'quantity',
        'cost'
    ];
    public function decorationCategory(){
        return $this->belongsTo(DecorationCategory::class);
    }
    public function decorationItemReservations(){
        return $this->hasMany(DecorationItemReservation::class);
    }
}
