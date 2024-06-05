<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DecorationCategory extends Model
{
    use HasFactory;
    protected  $fillable=[
        'name',
        'description_en',
        'description_ar',
        'icon'
    ];
    public function decorationItmes(){
        return $this->hasMany(DecorationItem::class);
    }
}
