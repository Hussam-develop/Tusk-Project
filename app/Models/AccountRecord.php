<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AccountRecord extends Model
{

    protected $fillable = [

        'dentist_id',
        'lab_manager_id',
        'bill_id',

        'creatorable_id', // Lab-Manager or Accountant
        'creatorable_type',

        'note',
        'type',
        'signed_value',
        'current_account',

        'created_at',
        'updated_at'

    ];
    protected $with = [
        // 'dentist',
        // 'labManager',
        // 'creator',
        // 'bill'

    ];

    public function dentist()
    {
        return $this->belongsTo(Dentist::class);
    }
    public function labManager()
    {
        return $this->belongsTo(LabManager::class);
    }
    public function bill()
    {
        return $this->hasOne(Bill::class, 'bill_id');
    }

    // Morph :

    public function creatorable(): MorphTo // Morph with : 1.Lab-Manager 2.Accountant
    {
        return $this->morphTo();
    }
}
