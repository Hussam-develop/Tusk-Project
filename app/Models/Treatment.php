<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Treatment extends Model
{
    protected $fillable = [
        'patient_id',
        'dentist_id',
        'medical_case_id',
        'cost',
        // 'title',
        'type',
        'details',
        'date',
        'is_paid'

    ];
    public function images()
    {
        return $this->hasMany(TreatmentImage::class);
    }
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
    public function dentist()
    {
        return $this->belongsTo(Dentist::class);
    }
    public function medical_case()
    {
        return $this->hasOne(MedicalCase::class);
    }
}
