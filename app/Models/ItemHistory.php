<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemHistory extends Model
{

    protected $fillable = [

        'item_id',

        'quantity',
        'unit_price',
        // 'date', not needed now
        // 'is_static', // Not needed here
        'total_price',
        'last_buying_date',


        'created_at',
        'updated_at'

    ];
    protected $with = [
        // 'item',

    ];
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
