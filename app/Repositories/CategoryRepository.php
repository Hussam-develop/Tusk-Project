<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\InventoryEmployee;
use App\Models\ItemHistory;
use App\Models\Subcategory;
use Carbon\Carbon;

class CategoryRepository
{
    public function getCategoriesForCurrentUser()
    {
        $user = auth()->user(); // المستخدم الحالي بعد تحديد Guard بواسطة Middleware
        $type = $user->getMorphClass();
        //  dd($type); // نوع المستخدم، مثلاً App\Models\Admin
        if ($type == "labManager") {

            // أولاً، نحصل على الـ ID الخاص بـ labManager
            $labManagerId = $user->id;

            // نحصل على جميع الموظفين الذين ينتمون لهذا المدير
            $employeeIds = InventoryEmployee::where('lab_manager_id', $labManagerId)
                ->pluck('id');

            // نستخدم الاستعلام لإيجاد الفئات التي تتطابق مع أن يكون نوعها إما 'labManager' أو 'inventory_employee'
            return Category::where(function ($query) use ($labManagerId, $employeeIds) {
                $query->where(function ($q) use ($labManagerId) {
                    $q->where('categoryable_type', 'labManager')
                        ->where('categoryable_id', $labManagerId);
                })
                    ->orWhere(function ($q) use ($employeeIds) {
                        $q->where('categoryable_type', 'inventoryEmployee')
                            ->whereIn('categoryable_id', $employeeIds);
                    });
            })
                ->get(['id', 'name']);
        } elseif ($type == "inventoryEmployee") {
            // لموظف المخزون، عرض الأصناف الخاصة به بالإضافة إلى أصناف المدير الذي يتبع له
            $employeeId = $user->id;

            // الحصول على الـ ID الخاص بمدير المخبر لهذا الموظف
            $labManagerId = InventoryEmployee::where('id', $employeeId)->value('lab_manager_id');
            $employeeIds = InventoryEmployee::where('lab_manager_id', $labManagerId)
                ->pluck('id');
            // عرض الأصناف الخاصة بالموظف والمدير
            return Category::where(function ($query) use ($labManagerId, $employeeIds) {
                $query->where(function ($q) use ($labManagerId) {
                    $q->where('categoryable_type', 'labManager')
                        ->where('categoryable_id', $labManagerId);
                })
                    ->orWhere(function ($q) use ($employeeIds) {
                        $q->where('categoryable_type', 'inventoryEmployee')
                            ->whereIn('categoryable_id', $employeeIds);
                    });
            })
                ->get(['id', 'name']);
        }

        return Category::where('categoryable_id', $user->id)
            ->where('categoryable_type', $type)
            ->get(['id', 'name']);
    }
    public function create($id, $type, array $data)
    {
        $data['categoryable_id'] = $id;
        $data['categoryable_type'] = $type;

        return Category::create($data);
    }
    public function deleteCategory($userid, $type, $id)
    {

        $category = Category::find($id);

        if ($category) {
            $category->delete();
            $category->subcategories()->delete();
            return true;
        }

        return false;
    }

    public function findCategoryById($id)
    {
        return Category::find($id);
    }

    public function updateCategory(Category $category, array $data)
    {
        return $category->update($data);
    }
    public function categories_and_total_prices($result2)
    {
        $user = auth()->user(); // المستخدم الحالي بعد تحديد Guard بواسطة Middleware
        $type = $user->getMorphClass();
        if ($type == "labManager") {
            $labManagerId = $user->id;
            // نحصل على جميع الموظفين الذين ينتمون لهذا المدير
            $employeeIds = InventoryEmployee::where('lab_manager_id', $labManagerId)
                ->pluck('id');
            $categories = \App\Models\Category::where(function ($query) use ($labManagerId) {
                $query->where('categoryable_type', 'labManager')
                    ->where('categoryable_id', $labManagerId);
            })
                ->orWhere(function ($query) use ($employeeIds) {
                    $query->where('categoryable_type', 'inventoryEmployee')
                        ->whereIn('categoryable_id', $employeeIds);
                })
                ->with('subcategories')
                ->get();
        } elseif ($type == "inventoryEmployee") {
            // لموظف المخزون، عرض الأصناف الخاصة به بالإضافة إلى أصناف المدير الذي يتبع له
            $employeeId = $user->id;

            // الحصول على الـ ID الخاص بمدير المخبر لهذا الموظف
            $labManagerId = InventoryEmployee::where('id', $employeeId)->value('lab_manager_id');
            $employeeIds = InventoryEmployee::where('lab_manager_id', $labManagerId)
                ->pluck('id');
            $categories = \App\Models\Category::where(function ($query) use ($labManagerId) {
                $query->where('categoryable_type', 'labManager')
                    ->where('categoryable_id', $labManagerId);
            })
                ->orWhere(function ($query) use ($employeeIds) {
                    $query->where('categoryable_type', 'inventoryEmployee')
                        ->whereIn('categoryable_id', $employeeIds);
                })
                ->with('subcategories')
                ->get();
        }
        // بادئ الأمر، نجلب جميع الفئات

        // تهيئة مصفوفة الناتج
        $result = [];

        foreach ($categories as $category) {
            // حساب المجموعات لكل فئة
            $totalPrice = 0;
            $totalQuantity = 0;

            foreach ($category->subcategories as $subcategory) {
                // استرجاع النتائج التي تتطابق مع الـ subcategory_id الحالي
                $matchingResults = array_filter($result2, function ($item) use ($subcategory) {
                    return $item['subcategory_id'] == $subcategory->id;
                });

                // جمع القيم
                foreach ($matchingResults as $item) {
                    $totalPrice += $item['total_price_last_month'];
                    $totalQuantity += $item['total_quantity_last_month'];
                }
            }

            // إضافة البيانات إلى الناتج إذا كانت هناك نتائج
            // if ($totalPrice > 0 || $totalQuantity > 0) {
            $result[] = [
                'category_id' => $category->id,
                'category_name' => $category->name,
                'total_price_last_month' => $totalPrice,
                'total_quantity_last_month' => $totalQuantity,
            ];
            // }
        }

        return $result;
    }
    public function all_total_prices_fo_all_subcategories1($user_id, $type)
    {
        if ($type == "labManager") {
            $labManagerId = $user_id;
            // نحصل على جميع الموظفين الذين ينتمون لهذا المدير
            $employeeIds = InventoryEmployee::where('lab_manager_id', $labManagerId)
                ->pluck('id');

            $startLastMonth = Carbon::now()->subMonth()->startOfMonth();
            $endLastMonth = Carbon::now()->subMonth()->endOfMonth();

            $totalSum = ItemHistory::whereHas('item', function ($query) use ($labManagerId, $employeeIds) {
                $query->where('creatorable_type', 'labManager')
                    ->where('creatorable_id', $labManagerId)
                    ->orWhere(function ($query) use ($employeeIds) {
                        $query->where('creatorable_type', 'inventoryEmployee')
                            ->whereIn('creatorable_id', $employeeIds);
                    });
            })
                ->whereBetween('created_at', [$startLastMonth, $endLastMonth])
                ->sum('total_price');


            if ($totalSum == 0) {
                return 'ليس لديك كميات مواد';
            }
            return $totalSum;
        } elseif ($type == "inventoryEmployee") {
            // لموظف المخزون، عرض الأصناف الخاصة به بالإضافة إلى أصناف المدير الذي يتبع له
            $employeeId = $user_id;

            // الحصول على الـ ID الخاص بمدير المخبر لهذا الموظف
            $labManagerId = InventoryEmployee::where('id', $employeeId)->value('lab_manager_id');
            $employeeIds = InventoryEmployee::where('lab_manager_id', $labManagerId)
                ->pluck('id');
            $startLastMonth = Carbon::now()->subMonth()->startOfMonth();
            $endLastMonth = Carbon::now()->subMonth()->endOfMonth();

            $totalSum = ItemHistory::whereHas('item', function ($query) use ($labManagerId, $employeeIds) {
                $query->where('creatorable_type', 'labManager')
                    ->where('creatorable_id', $labManagerId)
                    ->orWhere(function ($query) use ($employeeIds) {
                        $query->where('creatorable_type', 'inventoryEmployee')
                            ->whereIn('creatorable_id', $employeeIds);
                    });
            })
                ->whereBetween('created_at', [$startLastMonth, $endLastMonth])
                ->sum('total_price');


            if ($totalSum == 0) {
                return 'ليس لديك كميات مواد';
            }
            return $totalSum;
        }
    }
    public function sub_categories_and_total_prices1($userid, $type)
    {

        $resultsArray = [];

        $now = Carbon::now();

        // تحديد بداية ونهاية الشهر الحالي
        $startOfCurrentMonth = $now->copy()->startOfMonth();

        // تحديد بداية ونهاية الشهر الماضي
        $startOfLastMonth = $startOfCurrentMonth->copy()->subMonth();
        $endOfLastMonth = $startOfCurrentMonth->copy()->subDay();
        if ($type == "labManager") {
            $labManagerId = $userid;
            // نحصل على جميع الموظفين الذين ينتمون لهذا المدير
            $employeeIds = InventoryEmployee::where('lab_manager_id', $labManagerId)
                ->pluck('id');
            $subcategories = Subcategory::whereHas('items', function ($query) use ($labManagerId, $employeeIds) {
                $query->where(function ($subQuery) use ($labManagerId, $employeeIds) {
                    // الحالة الأولى: creatorable_type = 'labManager' و creatorable_id = $labManagerId
                    $subQuery->where('creatorable_type', 'labManager')
                        ->where('creatorable_id', $labManagerId);
                })->orWhere(function ($subQuery) use ($labManagerId, $employeeIds) {
                    // الحالة الثانية: creatorable_type = 'inventoryEmployee' و creatorable_id داخل مجموعة $employeeIds
                    $subQuery->where('creatorable_type', 'inventoryEmployee')
                        ->whereIn('creatorable_id', $employeeIds);
                });
            })
                ->with(['items' => function ($query) use ($labManagerId, $employeeIds) {
                    $query->where(function ($subQuery) use ($labManagerId, $employeeIds) {
                        $subQuery->where('creatorable_type', 'labManager')
                            ->where('creatorable_id', $labManagerId);
                    })->orWhere(function ($subQuery) use ($labManagerId, $employeeIds) {
                        $subQuery->where('creatorable_type', 'inventoryEmployee')
                            ->whereIn('creatorable_id', $employeeIds);
                    })->with(['itemHistory']);
                }])->get();
        } elseif ($type == "inventoryEmployee") {
            // لموظف المخزون، عرض الأصناف الخاصة به بالإضافة إلى أصناف المدير الذي يتبع له
            $employeeId = $userid;

            // الحصول على الـ ID الخاص بمدير المخبر لهذا الموظف
            $labManagerId = InventoryEmployee::where('id', $employeeId)->value('lab_manager_id');
            $employeeIds = InventoryEmployee::where('lab_manager_id', $labManagerId)
                ->pluck('id');

            $subcategories = Subcategory::whereHas('items', function ($query) use ($labManagerId, $employeeIds) {
                $query->where(function ($subQuery) use ($labManagerId, $employeeIds) {
                    // الحالة الأولى: creatorable_type = 'labManager' و creatorable_id = $labManagerId
                    $subQuery->where('creatorable_type', 'labManager')
                        ->where('creatorable_id', $labManagerId);
                })->orWhere(function ($subQuery) use ($labManagerId, $employeeIds) {
                    // الحالة الثانية: creatorable_type = 'inventoryEmployee' و creatorable_id داخل مجموعة $employeeIds
                    $subQuery->where('creatorable_type', 'inventoryEmployee')
                        ->whereIn('creatorable_id', $employeeIds);
                });
            })
                ->with(['items' => function ($query) use ($labManagerId, $employeeIds) {
                    $query->where(function ($subQuery) use ($labManagerId, $employeeIds) {
                        $subQuery->where('creatorable_type', 'labManager')
                            ->where('creatorable_id', $labManagerId);
                    })->orWhere(function ($subQuery) use ($labManagerId, $employeeIds) {
                        $subQuery->where('creatorable_type', 'inventoryEmployee')
                            ->whereIn('creatorable_id', $employeeIds);
                    })->with(['itemHistory']);
                }])->get();
        }

        foreach ($subcategories as $subcategory) {
            $totalPriceForSubcategory = 0; // مجموع سعر المواد لكل صنف
            $totalQuantityForSubcategory = 0; // مجموع الكميات الموجبة خلال الشهر الماضي

            foreach ($subcategory->items as $item) {
                // حساب الـ histories خلال الشهر الماضي
                $monthlyHistories = $item->itemHistory->filter(function ($history) use ($startOfLastMonth, $endOfLastMonth) {
                    return $history->created_at >= $startOfLastMonth && $history->created_at <= $endOfLastMonth;
                });

                // حساب مجموع الـ total_price
                $itemTotalPrice = $monthlyHistories->sum('total_price');
                $totalPriceForSubcategory += $itemTotalPrice;

                // حساب مجموع الـ quantity فقط للمدخلات الموجبة
                $itemQuantity = $monthlyHistories->filter(function ($history) {
                    return $history->quantity > 0; // تأكد من أن الكمية موجبة
                })->sum('quantity');

                $totalQuantityForSubcategory += $itemQuantity;
            }

            $resultsArray[] = [
                'subcategory_id' => $subcategory->id,
                'subcategory_name' => $subcategory->name,
                'total_price_last_month' => $totalPriceForSubcategory,
                'total_quantity_last_month' => $totalQuantityForSubcategory,
            ];
        }

        return $resultsArray;
    }
}
