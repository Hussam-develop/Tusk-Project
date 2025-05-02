<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class IllnessPatient extends Pivot
{

    protected $fillable = [

        'patient_id',
        'illness_id',

        'other_illness',


        'created_at',
        'updated_at'

    ];
    protected $with = [];
}
