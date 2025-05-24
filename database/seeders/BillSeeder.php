<?php

namespace Database\Seeders;

use App\Models\Bill;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Bill::create([
        // 'dentist_id' => 1,
        // 'lab_manager_id' => 1,
        // 'creatorable_id' => 1,
        // 'creatorable_type' => 'dentist',
        // 'bill_number' => '1',
        // 'total_cost' => 3000000,
        // 'date_from' => '2025-05-01',
        // 'date_to' => '2025-05-31',
        // ]);
        // Bill::create([
        // 'dentist_id' => 1,
        // 'lab_manager_id' => 1,
        // 'creatorable_id' => 1,
        // 'creatorable_type' => 'dentist',
        // 'bill_number' => '2',
        // 'total_cost' => 5000000,
        // 'date_from' => '2025-06-01',
        // 'date_to' => '2025-06-20',
        // ]);
        Bill::create([
        'dentist_id' => 1,
        'lab_manager_id' => 1,
        'creatorable_id' => 1,
        'creatorable_type' => 'dentist',
        'bill_number' => '3',
        'total_cost' => 6000000,
        'date_from' => '2025-07-01',
        'date_to' => '2025-07-20',
        ]);
    }
}
