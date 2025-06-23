<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class LabManager extends Authenticatable implements JWTSubject
{
    public $timestamps = true;

    protected $fillable = [
        'full_name',
        'password',
        'register_subscription_duration',
        // 'phone',
        'email',
        'email_is_verified',
        'email_verified_at',
        'verification_code',
        'register_accepted',
        'remember_token',

        // lab details :

        'lab_name',
        'lab_address',
        'lab_province',
        'lab_phone',
        'register_date',
        'subscription_is_valid_now',
        'lab_logo',
        'lab_type',
        'work_from_hour',
        'work_to_hour',


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
            'lab_phone' => 'array'
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
        $builder->where('subscription_is_valid_now', 0);
        // ->where('register_accepted', 1)
        // ->where('email_is_verified', 1);
    }
    public function accountRecords()
    {
        return $this->hasMany(AccountRecord::class);
    }
    public function accountant()
    {
        return $this->hasOne(Accountant::class);
    }
    public function inventoryEmployee()
    {
        return $this->hasOne(InventoryEmployee::class);
    }
    public function bills()
    {
        return $this->hasMany(Bill::class);
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function dentist()
    {
        return $this->belongsToMany(Dentist::class, "dentist_labManagers", 'lab_manager_id', 'dentist_id');
    }
    public function medicalCases()
    {
        return $this->hasMany(MedicalCase::class);
    }

    // Morph :

    public function morphAccountRecords(): MorphMany
    {
        return $this->morphMany(AccountRecord::class, 'creatorable');
    }
    public function morphBills(): MorphMany
    {
        return $this->morphMany(Bill::class, 'creatorable');
    }
    public function categories(): MorphMany
    {
        return $this->morphMany(Category::class, 'categoryable');
    }
    public function history(): MorphMany
    {
        return $this->morphMany(History::class, 'userable');
    }
    public function items(): MorphMany
    {
        return $this->morphMany(Item::class, 'creatorable');
    }
    public function subscriptions(): MorphMany
    {
        return $this->morphMany(Subscription::class, 'subscriptionable');
    }
    public function operatingPayment()
    {
        return $this->morphMany(OperatingPayment::class, name: 'creatorable');
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
