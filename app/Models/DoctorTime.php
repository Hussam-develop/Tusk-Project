<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorTime extends Model
{
    protected $fillable = [
        "dentist_id",
        "day",
        "start_time",
        "end_time",
        "start_rest",
        "end_rest"

    ];
    public function dentist()
    {
        return $this->belongsTo(Dentist::class, "dentist_id");
    }
}
