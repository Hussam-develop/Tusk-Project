<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManufacturedStatistic extends Model
{
    protected $fillable = [

        'lab_manager_id',
        'medical_case_id',
        'piece_number',
        'manufactured_quantity',

        'created_at',
        'updated_at',
    ];
}
