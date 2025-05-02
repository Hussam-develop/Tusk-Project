<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Illness extends Model
{

    protected $fillable = [

        'illness_name',

        'created_at',
        'updated_at'

    ];
    protected $with = [
        // 'patients',

    ];
    public function patients()
    {
        return $this->belongsToMany(Patient::class, "illness_patients", 'illness_id', 'patient_id');
    }
}
