<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DecorationItemReservation extends Model
{
    use HasFactory;
    protected  $fillable=[
      'event_id',
        'decoration_item_id',
        'start_date',
        'end_date',
        'quantity',
        'cost'
    ];
    public function decorationItem(){
        return $this->belongsTo(DecorationItem::class);
    }
    public function event(){
        return $this->belongsTo(Event::class);
    }
}
