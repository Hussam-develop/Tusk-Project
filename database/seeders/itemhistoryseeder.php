<?php

namespace Database\Seeders;

use App\Models\ItemHistory;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class itemhistoryseeder extends Seeder
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
    }
}
