<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Category extends Model
{

    protected $fillable = [

        'categoryable_id',  // Lab-Manager or Dentist
        'categoryable_type',

        'name',


        'created_at',
        'updated_at'

    ];
    protected $with = [
        // 'subcategories',
        // 'items',
    ];
    public function subcategories()
    {
        return $this->hasMany(Subcategory::class);
    }
    public function items()
    {
        return $this->hasMany(Item::class);
    }

    // Morph

    public function categoryable(): MorphTo  // morph with : 1.Lab-Manager 2.Dentist
    {
        return $this->morphTo();
    }
}
