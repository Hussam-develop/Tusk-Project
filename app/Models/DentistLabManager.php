<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class DentistLabManager extends Pivot
{
    protected $table = "dentist_labmanagers";

    protected $fillable = [

        'lab_manager_id',
        'dentist_id',

        'request_is_accepted',

        'created_at',
        'updated_at'

    ];
    protected $with = [];
}
