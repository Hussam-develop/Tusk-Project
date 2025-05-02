<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Appointment extends Model
{

    protected $fillable = [

        'patient_id',

        'creatorable_id', // Dentist or Secretary
        'creatorable_type',

        'from',
        'to',
        'title',
        'details',
        'date',
        'cost',
        'is_paid',

        'created_at',
        'updated_at'

    ];
    protected $with = [
        'images'
        // 'patient',
        // 'creatorable'
    ];
    public function images()
    {
        return $this->hasMany(AppointmentImage::class);
    }
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
