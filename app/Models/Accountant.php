<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Accountant extends Authenticatable implements JWTSubject
{
    protected $table = 'accountants';
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
        'work_start_at'
    ];

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


    public function labManager()
    {
        return $this->belongsTo(LabManager::class);
    }

    // Morph :

    public function morphAccountRecords()
    {
        return $this->morphMany(AccountRecord::class, 'creatorable');
    }
    public function morphBills()
    {
        return $this->morphMany(Bill::class, 'creatorable');
    }

    public function history()
    {
        return $this->morphMany(History::class, 'userable');
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
