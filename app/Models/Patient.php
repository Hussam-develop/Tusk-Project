<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{

    protected $fillable = [

        'dentist_id',

        'first_name',
        'last_name',
        'phone',
        'birthday',
        'current _balance',
        'is_smoker',
        'address',
        'gender',


        'created_at',
        'updated_at'

    ];
    protected $with = [
        // 'appointments',
        // 'medicalCases',
        // 'medicines',
        // 'dentist',
        // 'illnesses',
        // 'patientPayments',
    ];
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
    public function medicalCases()
    {
        return $this->hasMany(MedicalCase::class);
    }
    public function medicines()
    {
        return $this->hasMany(Medicine::class);
    }
    public function dentist()
    {
        return $this->belongsTo(Dentist::class);
    }
    public function illnesses()
    {
        return $this->belongsToMany(Illness::class, "illness_patients", 'patient_id', 'illness_id');
    }
    public function patientPayments()
    {
        return $this->hasMany(PatientPayment::class);
    }
}
