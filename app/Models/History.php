<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class History extends Model
{

    protected $fillable = [

        'userable_id', // morph with : 1.Admin 2.labManager 3.Dentist 4.Secretary 5.Accountant 6.InventoryEmployee
        'userable_type',

        'operation_name',
        'details',
        'date_time',

        'created_at',
        'updated_at'

    ];
    protected $with = [
        // 'userable',

    ];

    // morph with : 1.Admin 2.labManager 3.Dentist 4.Secretary 5.Accountant 6.InventoryEmployee
    public function userable(): MorphTo
    {
        return $this->morphTo();
    }
}
