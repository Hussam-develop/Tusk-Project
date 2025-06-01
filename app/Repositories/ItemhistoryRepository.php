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
}
