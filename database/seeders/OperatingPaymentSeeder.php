<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OperatingPaymentSeeder extends Seeder
{
    public function run()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $previousMonth = $currentMonth - 1;
        $previousYear = $currentMonth === 1 ? $currentYear - 1 : $currentYear;

        // 8 سجلات للشهر الماضي
        for ($i = 0; $i < 8; $i++) {
            DB::table('operating_payments')->insert([
                'creatorable_id' => 1,
                'creatorable_type' => $i < 5 ? 'dentist' : 'lab_manager', // 5 dentist و 3 lab_manager
                'name' => 'Service ' . ($i + 1),
                'value' => rand(100, 500), // قيمة عشوائية
                'created_at' => Carbon::create($previousYear, $previousMonth, rand(1, 28)),
                'updated_at' => Carbon::now(),
            ]);
        }

        // 2 سجلات للشهر الحالي
        for ($j = 0; $j < 2; $j++) {
            DB::table('operating_payments')->insert([
                'creatorable_id' => 1,
                'creatorable_type' => $j < 1 ? 'dentist' : 'lab_manager', // مثال
                'name' => 'Service Current ' . ($j + 1),
                'value' => rand(100, 500), // قيمة عشوائية
                'created_at' => Carbon::create($currentYear, $currentMonth, rand(1, 28)),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
