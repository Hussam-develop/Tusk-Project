<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Subscription extends Model
{

    protected $fillable = [

        'subscriptionable_id', // LabManager or Dentist
        'subscriptionable_type',

        'subscription_from',
        'subscription_to',
        'subscription_is_valid',
        'subscription_value',

        'created_at',
        'updated_at'

    ];
    protected $with = [
        // 'subscriptionable',

    ];

    // Morph :
    public function subscriptionable(): MorphTo // Morph with : 1.Lab-Manager 2.Dentist
    {
        return $this->morphTo();
    }
}
