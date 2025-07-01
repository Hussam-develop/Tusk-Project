<?php

namespace App\Repositories;

use App\Models\Item;
use App\Models\Subcategory;
use App\Models\InventoryEmployee;


class ItemRepository
{
    public function getItemsBySubcategory($subcategoryId)
    {

        // if($type =="labManager")
        // {
        //     $labManagerId = $id;
        //     $employeeIds = InventoryEmployee::where('lab_manager_id', $labManagerId)
        //                                     ->pluck('id');
        //     $subcategory = Subcategory::where('id', $subcategoryId)
        //     ->with(['items' => function ($query) use ($id, $type,$labManagerId,$employeeIds) {
        //         $query->where(function($q) use ($labManagerId) {
        //                 $q->where('creatorable_type', 'labManager')
        //                 ->where('creatorable_id', $labManagerId);
        //             })
        //             ->orWhere(function($q) use ($employeeIds) {
        //                 $q->where('creatorable_type', 'inventoryEmployee')
        //                 ->whereIn('creatorable_id', $employeeIds);
        //             });
        //     }])
        //     ->first();
        // }
        // elseif ($type == "inventoryEmployee")
        // {
        //     $employeeId = $id;
        //     $labManagerId = InventoryEmployee::where('id', $employeeId)->value('lab_manager_id');
        //     $employeeIds = InventoryEmployee::where('lab_manager_id', $labManagerId)
        //                                             ->pluck('id');
        //     $subcategory = Subcategory::where('id', $subcategoryId)
        //     ->with(['items' => function ($query) use ($id, $type,$labManagerId,$employeeIds) {
        //         $query->where(function($q) use ($labManagerId) {
        //                 $q->where('creatorable_type', 'labManager')
        //                 ->where('creatorable_id', $labManagerId);
        //             })
        //             ->orWhere(function($q) use ($employeeIds) {
        //                 $q->where('creatorable_type', 'inventoryEmployee')
        //                 ->whereIn('creatorable_id', $employeeIds);
        //             });
        //     }])
        //     ->first();
        // }
        // else{
        //     $subcategory = Subcategory::where('id', $subcategoryId)
        //     ->with(['items' => function ($query) use ($id, $type) {
        //         $query->where('creatorable_id', $id)
        //             ->where('creatorable_type', $type)
        //             ->get();
        //     }])
        //     ->first();

        // }


        // if ($subcategory) {
        //     return $subcategory->items; // تلقائياً بعد التصفية
        // }

        // return collect(); // أو [] إذا لم توجد
        $Subcategory = Subcategory::where('id', $subcategoryId)->with(['items' => function ($query) {
            // تحميل آخر سجل ItemHistory لكل item
            $query->with(['itemHistory' => function ($q) {
                $q->orderBy('created_at', 'desc')->limit(1);
            }]);
        }])->first();

        if ($Subcategory) {
            // لإرجاع الأصناف مع أهم بيانات History
            $itemsWithHistory = $Subcategory->items->map(function ($item) {
                // الحصول على آخر سجل ItemHistory
                $latestHistory = $item->itemHistory->first();

                return [
                    'item' => $item,
                    'unit_price' => $latestHistory ? $latestHistory->unit_price : null,
                    'created_at' => $latestHistory ? $latestHistory->created_at : null,
                ];
            });

            return $itemsWithHistory;
        }


        // في حال لم توجد الفئة
        return collect();
    }
    public function Verify_permission_to_add_item($subcategoryId)
    {
        $subcategory = Subcategory::where('id', $subcategoryId)->first();

        if ($subcategory) {

            return true;
        }
        return false;
    }

    public function addItem($subcategoryId, $data, $creatorable_id, $creatorable_type)
    {
        $subcategory = Subcategory::where('id', $subcategoryId)->first();
        $category = $subcategory->category;
        if ($category) {
            $categoryid = $category->id;
        }
        $data['subcategory_id'] = $subcategoryId;
        $data['category_id'] = $categoryid;
        $data['creatorable_id'] = $creatorable_id;
        $data['creatorable_type'] = $creatorable_type;
        $data['quantity'] = 0;
        $res = Item::create($data);
        if ($res) {
            return true;
        }
        return false;
    }

    public function deleteItem($userid, $type, $id)
    {

        $item = Item::where('id', $id)->first();

        if ($item) {
            $item->delete();
            return true;
        }
        return false;
    }

    public function findItemById($id)
    {
        return Item::find($id);
    }
    public function updateItem($item, $data)
    {
        if ($item) {
            $data['quantity'] = 0;
            $item->update($data);
            return true;
        }

        return false;
    }
    public function items_of_user($user_id, $type)
    {
        if ($type == "labManager") {

            // أولاً، نحصل على الـ ID الخاص بـ labManager
            $labManagerId = $user_id;

            // نحصل على جميع الموظفين الذين ينتمون لهذا المدير
            $employeeIds = InventoryEmployee::where('lab_manager_id', $labManagerId)
                ->pluck('id');

            // نستخدم الاستعلام لإيجاد الفئات التي تتطابق مع أن يكون نوعها إما 'labManager' أو 'inventory_employee'
            return Item::where(function ($query) use ($labManagerId, $employeeIds) {
                $query->where(function ($q) use ($labManagerId) {
                    $q->where('creatorable_type', 'labManager')
                        ->where('creatorable_id', $labManagerId);
                })
                    ->orWhere(function ($q) use ($employeeIds) {
                        $q->where('creatorable_type', 'inventoryEmployee')
                            ->whereIn('creatorable_id', $employeeIds);
                    });
            })
                ->get(['id', 'name']);
        } elseif ($type == "inventoryEmployee") {
            // لموظف المخزون، عرض الأصناف الخاصة به بالإضافة إلى أصناف المدير الذي يتبع له
            $employeeId = $user_id;

            // الحصول على الـ ID الخاص بمدير المخبر لهذا الموظف
            $labManagerId = InventoryEmployee::where('id', $employeeId)->value('lab_manager_id');
            $employeeIds = InventoryEmployee::where('lab_manager_id', $labManagerId)
                ->pluck('id');
            // عرض الأصناف الخاصة بالموظف والمدير
            return Item::where(function ($query) use ($labManagerId, $employeeIds) {
                $query->where(function ($q) use ($labManagerId) {
                    $q->where('creatorable_type', 'labManager')
                        ->where('creatorable_id', $labManagerId);
                })
                    ->orWhere(function ($q) use ($employeeIds) {
                        $q->where('creatorable_type', 'inventoryEmployee')
                            ->whereIn('creatorable_id', $employeeIds);
                    });
            })
                ->get(['id', 'name']);
        }
        return false;
    }
}
