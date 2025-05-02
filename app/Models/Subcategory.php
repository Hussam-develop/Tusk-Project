<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{

    protected $fillable = [

        'category_id',


        'name',

        'created_at',
        'updated_at'

    ];
    protected $with = [
        'category',
        'items',
    ];
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
