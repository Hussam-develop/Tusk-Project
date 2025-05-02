<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillCase extends Model
{

    protected $fillable = [

        'bill_id',
        'medical_case_id',

        'case_cost',

        'created_at',
        'updated_at'

    ];
    protected $with = [
        // 'bill',
        // 'medical_case',

    ];
    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }
    public function medicalCase()
    {
        return $this->belongsTo(MedicalCase::class);
    }
}
