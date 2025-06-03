<?php

namespace App\Repositories;

use App\Models\Item;
use App\Models\ItemHistory;

class ItemhistoryRepository
{
    public function Verify_permission_to_add_itemhistory($itemid, $id, $type)
    {
        $item = Item::where('id', $itemid)->first();

        if (
            $item && $item->creatorable_id == $id &&
            $item->creatorable_type == $type
        ) {


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

    public function itemhistories($itemId, $id, $type)
    {
        $item = Item::where('id', $itemId)->where('creatorable_id', $id)
            ->where('creatorable_type', $type)
            ->first();



        if ($item) {
            return $item->itemHistory; // تلقائياً بعد التصفية
        }

        return collect(); // أو [] إذا لم توجد
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
}
