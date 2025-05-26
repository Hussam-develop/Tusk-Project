<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class OperatingPayment extends Model
{
    protected $fillable = [
        'creatorable_id',
        'creatorable_type',

        'name',
        'value',

    ];
    public function creatorable(): MorphTo
    {
        return $this->morphTo();
    }
}
