<?php

namespace Database\Seeders;

use App\Models\DentistLabManager;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DentistLabManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         DentistLabManager::create([
            'lab_manager_id' => 3,
            'dentist_id' => 1,
            'request_is_accepted'=> 1,
        ]);
         DentistLabManager::create([
            'lab_manager_id' => 4,
            'dentist_id' => 1,
            'request_is_accepted'=> 1,
        ]);
         DentistLabManager::create([
            'lab_manager_id' => 5,
            'dentist_id' => 1,
            'request_is_accepted'=> 1,
        ]);

    }
}
