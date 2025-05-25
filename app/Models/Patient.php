<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{

    protected $fillable = [

        'dentist_id',

        'full_name',
        'phone',
        'birthday',
        'current_balance',
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

    public function medicalCases()
    {
        return $this->hasMany(MedicalCase::class);
    }
    public function dentist()
    {
        return $this->belongsTo(Dentist::class);
    }

    public function patientPayments()
    {
        return $this->hasMany(PatientPayment::class);
    }
    public function treatments()
    {
        return $this->hasMany(Treatment::class);
    }
}
