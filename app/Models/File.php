<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{

    protected $fillable = [

        'medical_case_id',

        'name',
        'is_case_image',

        'created_at',
        'updated_at'

    ];
    protected $with = [
        // 'medicalCase',

    ];
    public function medicalCase()
    {
        return $this->belongsTo(MedicalCase::class);
    }
}
