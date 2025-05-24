<?php

namespace Database\Seeders;

use App\Models\Subcategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         Subcategory::create([
            'category_id' => 3,
            'name' => 'مرايا ',


        ]);
        Subcategory::create([
            'category_id' => 3,
            'name' => 'كماشات',


        ]);
        Subcategory::create([
            'category_id' => 3,
            'name' => ' حشوات',


        ]);
         Subcategory::create([
            'category_id' => 3,
            'name' => ' اضوية',


        ]);
        Subcategory::create([
            'category_id' => 4,
            'name' => 'سنابل',


        ]);
        Subcategory::create([
            'category_id' => 4,
            'name' => 'نكاشات',


        ]);

    }
}
