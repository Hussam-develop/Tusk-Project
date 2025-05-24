<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class itemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Item::create([
            'category_id' => 3,
            'subcategory_id' => 3,
            'creatorable_id'=> 1,
            'creatorable_type'=> 'dentist',
            'name'=> 'حشوة كومبوسيت',
            'quantity'=> '10',
            'standard_quantity'=> '5',
            'minimum_quantity'=> '2',
            'unit'=> 'ماسورة',
            'is_static'=> 1,
        ]);

         Item::create([
            'category_id' => 3,
            'subcategory_id' => 3,
            'creatorable_id'=> 1,
            'creatorable_type'=> 'dentist',
            'name'=> 'حشوة تجميلية',
            'quantity'=> '10',
            'standard_quantity'=> '5',
            'minimum_quantity'=> '2',
            'unit'=> 'علبة',
            'is_static'=> 1,
        ]);


    }
}
