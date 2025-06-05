<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Appointment extends Model
{

    protected $fillable = [

        'dentist_id',
        'patient_name',
        'patient_phone',
        'date',
        'time_from',
        'time_to',
        'creatorable_id',
        'creatorable_type',
        'created_at',
        'updated_at'

    ];
    protected $with = [
        // 'images'
        // 'patient',
        // 'creatorable'
    ];
    // public function images()
    // {
    //     return $this->hasMany(AppointmentImage::class);
    // }
    // public function patient()
    // {
    //     return $this->belongsTo(Patient::class);
    // }

    // Morph :
    public function creatorable(): MorphTo // Morph with : 1.Dentist 2.Secretary
    {
        return $this->morphTo();
    }
}
