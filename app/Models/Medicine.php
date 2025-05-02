<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{

    protected $fillable = [

        'patient_id',

        'medicine_name',

        'created_at',
        'updated_at'

    ];
    protected $with = [
        // 'patient',

    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
