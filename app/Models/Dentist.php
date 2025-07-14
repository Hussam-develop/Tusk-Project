<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Dentist extends Authenticatable implements JWTSubject
{
    public $timestamps = true;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'register_subscription_duration',
        'image_path',

        // 'work_from_hour',
        // 'work_to_hour',

        'phone', // phone clinic
        'address', //clinic address
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
    public function scopeSubscriptionIsValidNow(Builder $builder)
    {
        $builder->where('register_accepted', 1)
            ->where('subscription_is_valid_now', 1)
            ->where('email_is_verified', 1);
    }
    public function scopeSubscription_NOT_ValidNow(Builder $builder)
    {
        $builder
            ->where('subscription_is_valid_now', 0);
        // ->where('register_accepted', 1)
        //     ->where('email_is_verified', 1);
    }

    /// relations
    public function latestAccountRecord()
    {
        return $this->hasOne(AccountRecord::class)->latestOfMany();
    }
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
    public function lab()
    {
        return $this->belongsToMany(LabManager::class, "dentist_labManagers", 'dentist_id', 'lab_manager_id')
            ->using(DentistLabManager::class)
            ->withPivot('request_is_accepted', 'created_at', 'updated_at');
    }
    public function medicalCases()
    {
        return $this->hasMany(MedicalCase::class);
    }

    public function treatments()
    {
        return $this->hasMany(Treatment::class);
    }
    public function doctorTimes()
    {
        return $this->hasMany(DoctorTime::class, "dentist_id");
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
    public function operatingPayment()
    {
        return $this->morphMany(OperatingPayment::class, name: 'creatorable');
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
