<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Secretary extends Authenticatable implements JWTSubject
{
    protected $fillable = [
        'dentist_id',

        'first_name',
        'last_name',
        'address',
        'is_staged',
        'password',
        'attendence_time',
        'phone',
        'email',
        'rememberToken',
        'email_is_verified',
        'email_verified_at',
        'verification_code',

    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];


    public function dentist()
    {
        return $this->belongsTo(Dentist::class);
    }

    // Morph :
    public function patientPayments()
    {
        return $this->morphMany(PatientPayment::class, 'creatorable');
    }
    public function appointments()
    {
        return $this->morphMany(Appointment::class, 'creatorable');
    }
    public function history()
    {
        return $this->morphMany(History::class, 'userable');
    }

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
