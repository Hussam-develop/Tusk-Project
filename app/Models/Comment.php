<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{

    protected $fillable = [

        'medical_case_id',
        'dentist_id',
        'lab_manager_id',

        'comment',

        'created_at',
        'updated_at'

    ];
    protected $with = [
        // 'medicalCase',
        // 'dentist',
        // 'labManager'

    ];

    public function medicalCase()
    {
        return $this->belongsTo(MedicalCase::class);
    }
    public function dentist()
    {
        return $this->belongsTo(Dentist::class);
    }
    public function labManager()
    {
        return $this->belongsTo(LabManager::class);
    }
}
