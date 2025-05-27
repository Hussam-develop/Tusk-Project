<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PatientPayment extends Model
{
    public $timestamps = false;
    protected $fillable = [

        'patient_id',
        'dentist_id',

        'creatorable_id', // Dentist Or Secretary
        'creatorable_type',

        'payment_date',
        'value',

    ];
    protected $with = [
        // 'patient',

    ];
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    // Morph :
    public function creatorable(): MorphTo // Morph with : 1.Dentist 2.Secretary
    {
        return $this->morphTo();
    }
}
