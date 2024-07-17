<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FillHistory extends Model
{
    use HasFactory;
    protected $fillable=[
        'station_id',
        'wallet_id',
        'quantity'
    ];
    public function station(){
        return $this->belongsTo(Station::class);

    }
    public function wallet(){
        return $this->belongsTo(Wallet::class);
    }
}
