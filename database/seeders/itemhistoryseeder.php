<?php

namespace Database\Seeders;

use App\Models\ItemHistory;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Itemhistoryseeder extends Seeder
{
    public function run(): void
    {
        // استرجاع جميع السجلات
        $items = ItemHistory::all();

        foreach ($items as $item) {
            // خصم شهر واحد من التواريخ
            $newCreatedAt = Carbon::parse($item->created_at)->subMonth();
            $newUpdatedAt = Carbon::parse($item->updated_at)->subMonth();

            // تحديث القيم
            $item->update([
                'created_at' => $newCreatedAt,
                'updated_at' => $newUpdatedAt,
            ]);
        }






        $records = [];

        // شهر 1 - 1 سطر
        $records[] = [
            'item_id' => 56,
            'quantity' => rand(1, 10),
            'unit_price' => rand(5, 15),
            'total_price' => rand(20, 150),
            'created_at' => Carbon::create(2025, 1, 1, 10, 0),
            'updated_at' => Carbon::create(2025, 1, 1, 10, 0),
        ];

        // شهر 2 - 2 سطر
        for ($i = 1; $i <= 2; $i++) {
            $records[] = [
                'item_id' => 56,
                'quantity' => rand(1, 10),
                'unit_price' => rand(5, 15),
                'total_price' => rand(20, 150),
                'created_at' => Carbon::create(2025, 2, $i * 3, 12, 0),
                'updated_at' => Carbon::create(2025, 2, $i * 3, 12, 0),
            ];
        }

        // شهر 3 - 3 سطر
        for ($i = 1; $i <= 3; $i++) {
            $records[] = [
                'item_id' => 56,
                'quantity' => rand(1, 10),
                'unit_price' => rand(5, 15),
                'total_price' => rand(20, 150),
                'created_at' => Carbon::create(2025, 3, $i * 5, 9, 0),
                'updated_at' => Carbon::create(2025, 3, $i * 5, 9, 0),
            ];
        }

        // شهر 4 - 4 سطر
        for ($i = 1; $i <= 4; $i++) {
            $records[] = [
                'item_id' => 56,
                'quantity' => rand(1, 10),
                'unit_price' => rand(5, 15),
                'total_price' => rand(20, 150),
                'created_at' => Carbon::create(2025, 4, $i * 6, 14, 0),
                'updated_at' => Carbon::create(2025, 4, $i * 6, 14, 0),
            ];
        }

        // شهر 5 - 5 سطر
        for ($i = 1; $i <= 5; $i++) {
            $records[] = [
                'item_id' => 56,
                'quantity' => rand(1, 10),
                'unit_price' => rand(5, 15),
                'total_price' => rand(20, 150),
                'created_at' => Carbon::create(2025, 5, $i * 4, 11, 0),
                'updated_at' => Carbon::create(2025, 5, $i * 4, 11, 0),
            ];
        }

        // شهر 6 - 2 سطر
        for ($i = 1; $i <= 2; $i++) {
            $records[] = [
                'item_id' => 56,
                'quantity' => rand(1, 10),
                'unit_price' => rand(5, 15),
                'total_price' => rand(20, 150),
                'created_at' => Carbon::create(2025, 6, $i * 7, 16, 0),
                'updated_at' => Carbon::create(2025, 6, $i * 7, 16, 0),
            ];
        }

        // حفظ البيانات في قاعدة البيانات
        foreach ($records as $record) {
            ItemHistory::create($record);
        }
    }
}
