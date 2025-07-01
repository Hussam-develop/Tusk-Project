<?php

namespace App\Repositories;

use App\Models\Item;
use App\Models\ItemHistory;

class ItemhistoryRepository
{
    public function Verify_permission_to_add_itemhistory($itemid)
    {
        $item = Item::where('id', $itemid)->first();

        if ($item) {
            return true;
        }

        return false;
    }

    public function addItemhistory($item_id, $data)
    {
        $item = Item::where('id', $item_id)->first();
        if ($item) {
            $item->quantity  = $item->quantity + $data['quantity'];
            $item->save();
            $data['item_id'] = $item_id;

            $res = ItemHistory::create($data);
        }

        if ($res) {
            return true;
        }
        return false;
    }

    public function itemhistories($itemId)
    {
        // if($type =="labManager")
        // {
        //     $labManagerId =$id;
        //     $employeeIds = InventoryEmployee::where('lab_manager_id', $labManagerId)
        //                                     ->pluck('id');
        //     $item = Item::where(function($query) use ($labManagerId, $employeeIds)
        //     {
        //             $query->where(function($q) use ($labManagerId) {
        //                 $q->where('creatorable_type', 'labManager')
        //                 ->where('creatorable_id', $labManagerId);
        //             })
        //             ->orWhere(function($q) use ($employeeIds) {
        //                 $q->where('creatorable_type', 'inventoryEmployee')
        //                 ->whereIn('creatorable_id', $employeeIds);
        //             });
        //         })
        //     ->with('itemHistory') // تسجيل العلاقة المضافة
        //     ->first();
        // }
        // elseif ($type == "inventoryEmployee") {
        //         $employeeId=$id;
        //         $labManagerId = InventoryEmployee::where('id', $employeeId)->value('lab_manager_id');
        //         $item = Item::where(function($query) use ($labManagerId, $employeeIds)
        //     {
        //             $query->where(function($q) use ($labManagerId) {
        //                 $q->where('creatorable_type', 'labManager')
        //                 ->where('creatorable_id', $labManagerId);
        //             })
        //             ->orWhere(function($q) use ($employeeIds) {
        //                 $q->where('creatorable_type', 'inventoryEmployee')
        //                 ->whereIn('creatorable_id', $employeeIds);
        //             });
        //         })
        //     ->with('itemHistory') // تسجيل العلاقة المضافة
        //     ->first();
        // }
        $item = Item::where('id', $itemId)->first();

        if ($item) {
            // ترتيب التاريخ من الأقدم للأحدث
            $itemhistories = $item->itemHistory->sortBy('created_at');

            $cumulativeValue = 0;
            $result = [];

            foreach ($itemhistories as $history) {
                // إضافة كمية السجل إلى القيمة التراكمية
                $cumulativeValue += $history->quantity;

                // حساب 'recent_value' كـ 'new_value - quantity'
                $recent_value = $cumulativeValue - $history->quantity;

                // إضافة البيانات إلى النتيجة
                $result[] = [
                    'id' => $history->id,
                    'created_at' => $history->created_at,
                    'quantity' => $history->quantity,
                    'new_value' => $cumulativeValue,
                    'recent_value' => $recent_value,
                ];
            }
            $reversedArray = array_reverse($result);
            return $reversedArray;
        }

        return collect(); // أو يمكنك إرجاع مصفوفة فارغة [] إذا تفضل
    }
    public function Repeated_item_histories($user_id, $type)
    {
        if ($type != 'dentist') {
            return 'ليس طبيب';
        }
        $itemHistories = ItemHistory::with(['item' => function ($query) {
            $query->select('id', 'name'); // تأكد من أن 'id' موجود لأنه يستخدم للمطابقة مع foreign key
        }])
            ->whereHas('item', function ($query) use ($user_id, $type) {
                $query->where('is_static', 1)
                    ->where('creatorable_id', $user_id)
                    ->where('creatorable_type', $type);
            })
            ->orderBy('created_at', 'desc')
            ->get();



        return $itemHistories;
    }
    public function Non_Repeated_item_histories($user_id, $type)
    {
        if ($type != 'dentist') {
            return 'ليس طبيب';
        }
        $itemHistories = ItemHistory::with(['item' => function ($query) {
            $query->select('id', 'name'); // تأكد من أن 'id' موجود لأنه يستخدم للمطابقة مع foreign key
        }])
            ->whereHas('item', function ($query) use ($user_id, $type) {
                $query->where('is_static', 0)
                    ->where('creatorable_id', $user_id)
                    ->where('creatorable_type', $type);
            })
            ->orderBy('created_at', 'desc')
            ->get();



        return $itemHistories;
    }

    public function add_nonrepeated_itemhistory($user_id, $type, $data)
    {

        if ($type != 'dentist') {
            return 'ليس طبيب';
        }
        // التحقق إذا كان الاسم موجودًا بالفعل
        $existingItem = Item::whereRaw('SOUNDEX(name) = SOUNDEX(?)', [$data['name']])->first();
        if ($existingItem) {
            // إذا كان الاسم موجودًا، يمكنك التعامل حسب الحاجة
            // مثلاً، إرجاع رسالة أو تحديث السجل
            ItemHistory::create([
                'item_id' => $existingItem->id, // ربط الـ ItemHistory بالـ Item الذي أنشأته
                'quantity' => $data['quantity'],
                'total_price' => $data['total_price'],
                'unit_price' => 0,
            ]);
            return 'اسم العنصر موجود بالفعل.';
        } else {
            // إنشاء العنصر الجديد إذا لم يوجد
            $res1 = Item::create([
                'name' => $data['name'],
                'creatorable_id' => $user_id,
                'creatorable_type' => $type,
                'is_static' => 0,
            ]);
            $res2 = ItemHistory::create([
                'item_id' => $res1->id, // ربط الـ ItemHistory بالـ Item الذي أنشأته
                'quantity' => $data['quantity'],
                'total_price' => $data['total_price'],
                'unit_price' => 0,
            ]);
        }



        // بعد ذلك، أنشئ الـ ItemHistory ومرر الـ item_id

        return 'تم';
    }
    public function The_monthly_consumption_of_item($itemid)
    {
        // إنشاء مصفوفة تحتوي على جميع الأشهر من 1 إلى 12
        $allMonths = collect(range(1, 12));

        // استعلام للحصول على الكميات السالبة
        $stats = ItemHistory::where('item_id', $itemid)
            ->where('quantity', '<', 0) // فقط الكميات السالبة
            ->selectRaw("MONTH(created_at) as month, SUM(quantity) as total") // جمع الكميات السالبة
            ->groupBy('month')
            ->get()
            ->keyBy('month'); // لتحويل النتائج إلى مصفوفة مفهرسة بالمؤشر 'month'

        // دمج الأشهر مع النتائج لضمان وجود كل الأشهر
        $results = $allMonths->map(function ($month) use ($stats) {
            // إذا لم يوجد بيانات للشهر، القيمة تكون 0
            return [
                'month' => $month,
                'Negative quantity' => $stats->has($month) ? abs($stats[$month]['total']) : 0 // استخدام abs لجعل القيمة موجبة
            ];
        });

        // لتحويل النتيجة إلى مصفوفة أو عرضها كيفما تريد
        $finalResults = $results->all();
        return $finalResults;
    }
}
