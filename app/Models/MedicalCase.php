<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Types\Resource_;

class MedicalCase extends Model
{

    protected $fillable = [

        'dentist_id',
        'lab_manager_id',
        'patient_id',

        'age',
        'gender',
        'need_trial',
        'repeat',
        'shade',
        'expected_delivery_date',
        'notes',
        'status',
        'confirm_delivery',
        'cost',

        'teeth_crown',
        'teeth_pontic',
        'teeth_implant',
        'teeth_veneer',
        'teeth_inlay',
        'teeth_denture',

        'bridges_crown',
        'bridges_pontic',
        'bridges_implant',
        'bridges_veneer',
        'bridges_inlay',
        'bridges_denture',

        'created_at',
        'updated_at',
    ];

    protected $with = [
        // 'billCases',
        // 'comments',
        // 'dentist',
        // 'labManager',
        // 'patient',
        'files'
    ];

    public function billCases()
    {
        return $this->hasMany(BillCase::class);
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function dentist()
    {
        return $this->belongsTo(Dentist::class);
    }
    public function labManager()
    {
        return $this->belongsTo(LabManager::class);
    }
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
    public function files()
    {
        return $this->hasMany(File::class);
    }
}
