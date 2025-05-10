<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Dentist extends Authenticatable implements JWTSubject
{
    public $timestamps = false;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'image_path',
        'phone', // phone clinic
        'address', //clinic address
        'province',
        'email_is_verified',
        'email_verified_at',
        'verification_code',
        'register_accepted',
        'clinic_register_date',
        'subscription_is_valid_now',
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

    /// relations

    public function accountRecords()
    {
        return $this->hasMany(AccountRecord::class);
    }
    public function bills()
    {
        return $this->hasMany(Bill::class);
    }
    public function secretaries()
    {
        return $this->hasMany(Secretary::class);
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function patients()
    {
        return $this->hasMany(Patient::class);
    }
    public function labManager()
    {
        return $this->belongsToMany(LabManager::class, "dentist_labManagers", 'dentist_id', 'lab_manager_id');
    }
    public function medicalCases()
    {
        return $this->hasMany(MedicalCase::class);
    }

    //Morph
    public function patientPayments()
    {
        return $this->morphMany(PatientPayment::class, 'creatorable');
    }

    public function appointments()
    {
        return $this->morphMany(Appointment::class, 'creatorable');
    }
    public function categories()
    {
        return $this->morphMany(Category::class, 'categoryable');
    }
    public function history()
    {
        return $this->morphMany(History::class, 'userable');
    }
    public function items()
    {
        return $this->morphMany(Item::class, 'creatorable');
    }
    public function subscriptions()
    {
        return $this->morphMany(Subscription::class, 'subscriptionable');
    }


    // jwt integration
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
