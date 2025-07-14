<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Tymon\JWTAuth\Contracts\JWTSubject;

class InventoryEmployee extends Authenticatable implements JWTSubject
{
    public $timestamps = true;

    protected $fillable = [

        'lab_manager_id',

        'full_name',
        'active',
        'email',
        'password',
        'is_staged',
        'phone',
        'remember_token',
        'email_is_verified',
        'email_verified_at',
        'verification_code',
        'work_start_at',

        'created_at',
        'updated_at'

    ];
    protected $with = [
        // 'labManager',

    ];

    public function labManager()
    {
        return $this->belongsTo(LabManager::class);
    }

    // morph :
    public function history(): MorphMany
    {
        return $this->morphMany(History::class, 'userable');
    }
    public function items(): MorphMany
    {
        return $this->morphMany(Item::class, 'creatorable');
    }



    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
