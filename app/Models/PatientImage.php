<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientImage extends Model
{
    public $timestamps = false;

    protected $fillable = [

        'patient_id',

        'name',

    ];
    protected $with = [
        // 'patient'

    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
