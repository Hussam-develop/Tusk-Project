<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TreatmentImage extends Model
{
    public $timestamps = false;

    protected $fillable = [

        'treatment_id',

        'name',
        'is_diagram',

        'created_at',
        'updated_at'

    ];
    protected $with = [
        // 'treatment'

    ];

    public function treatment()
    {
        return $this->belongsTo(Treatment::class);
    }
}
