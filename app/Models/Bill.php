<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Bill extends Model
{

    protected $fillable = [

        'dentist_id',
        'lab_manager_id',

        'creatorable_id', //  Lab-Manager or Accountant
        'creatorable_type',

        'bill_number',
        'total_cost',
        'date_from',
        'date_to',

        'created_at',
        'updated_at'

    ];
    protected $with = [
        // 'accountRecord',
        // 'dentist',
        // 'labManager',
        // 'billCases'

    ];
    public function accountRecord()
    {
        return $this->belongsTo(AccountRecord::class,'bill_id');
    }
    public function dentist()
    {
        return $this->belongsTo(Dentist::class);
    }
    public function labManager()
    {
        return $this->belongsTo(LabManager::class);
    }
    public function billCases()
    {
        return $this->hasMany(BillCase::class);
    }

    // Morph :
    public function creatorable(): MorphTo  // morph with : 1.Lab-Manager  2.Accountant
    {
        return $this->morphTo();
    }
}
