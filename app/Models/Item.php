<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{

    protected $fillable = [

        'category_id',
        'subcategory_id',

        'creatorable_id', //Dentist or Lab-manager or inventory employee
        'creatorable_type',

        'name',
        'quantity',
        'standard_quantity',
        'minimum_quantity',
        'unit',
        'is_static',

        'created_at',
        'updated_at'

    ];
    protected $with = [
        // 'category',
        // 'subcategory',
        // 'itemHistory',
        // 'creatorable'

    ];
    public function itemHistory()
    {
        return $this->hasMany(ItemHistory::class);
    }
    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Morph :
    public function creatorable() // 1.Dentist 2.Lab-manager 3.inventoryEmployee
    {
        return $this->morphTo();
    }
}
