<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentImage extends Model
{

    protected $fillable = [

        'appointment_id',

        'name',
        'is_diagram',

        'created_at',
        'updated_at'

    ];
    protected $with = [
        // 'appointment'

    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}
