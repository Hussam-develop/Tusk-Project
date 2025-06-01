<?php

namespace Database\Seeders;

use App\Models\Category;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {

        // Category::create([
        //     'categoryable_id' => 1,
        //     'categoryable_type' => 'dentist',
        //     'name' => "مواد تخدير",


        // ]);
        // Category::create([
        //     'categoryable_id' => 1,
        //     'categoryable_type' => 'dentist',
        //     'name' => "مواد تعقيم",


        // ]);
        // Category::create([
        //     'categoryable_id' => 1,
        //     'categoryable_type' => 'dentist',
        //     'name' => "ادوات عمل",


        // ]);

        //  Category::create([
        //     'categoryable_id' => 3,
        //     'categoryable_type' => 'labManager',
        //     'name' => "مواد حفر",


        // ]);

        Category::create([
            'categoryable_id' => 3,
            'categoryable_type' => 'labManager',
            'name' => "مواد طباعة",


        ]);
    }
}
