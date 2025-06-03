<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\ItemHistory;
use App\Models\Subcategory;
use Carbon\Carbon;

class SubCategoryRepository
{
    public function getSubcategoriesByCategoryId($categoryId, $id, $type)
    {
        // أولاً، تأكد أن الفئة موجودة ومشروطة بالمستخدم والنوع
        $category = Category::where('id', $categoryId)
            ->where('categoryable_id', $id)
            ->where('categoryable_type', $type)
            ->with('subcategories') // تسجيل العلاقة المضافة
            ->first();

        // إذا وجد الفئة
        if ($category) {
            // إرجاع الأصناف الفرعية الخاصة بالفئة
            return $category->subcategories()->select('id', 'name')->get();
        }

        // في حال لم توجد الفئة
        return collect(); // أو [] حسب الأفضلية
    }

    public function deleteSubCategory($userid, $type, $id)
    {

        $subcategory = Subcategory::where('id', $id)->first();

        if ($subcategory) {
            $category = $subcategory->category; // هنا استدعاء العلاقة كـ نموذج، وليس كوظيفة
            if (
                $category &&
                $category->categoryable_id == $userid &&
                $category->categoryable_type == $type
            ) {

                $subcategory->delete();
                $subcategory->items()->delete();
                return true;
            }
        }
        return false;
    }

    public function Verify_permission_to_add_subcategory($categoryId, $id, $type)
    {
        // استرجاع جميع الفئات الخاصة بالمستخدم
        $categories = Category::where('categoryable_id', $id)
            ->where('categoryable_type', $type)
            ->get();

        // التحقق إذا كان $categoryId موجودًا ضمن الفئات التي تم إرجاعها
        $categoryIds = $categories->pluck('id')->toArray();

        if (in_array($categoryId, $categoryIds)) {
            // إذا كان موجودًا، استرجاع الـ Subcategories
            return true;
        } else {
            // إذا غير موجود، يمكن إرجاع مصفوفة فارغة أو رسالة
            return false; // أو: return [];
        }
    }
    public function addSubCategory($category_id, $data)
    {
        $data['category_id'] = $category_id;
        SubCategory::create($data);
        return true;
    }
    public function findSubCategoryById($id)
    {
        return SubCategory::find($id);
    }
    public function updateSubCategory($subcategory, $data, $userid, $type)
    {
        $category = $subcategory->category; // هنا استدعاء العلاقة كـ نموذج، وليس كوظيفة
        if (
            $category &&
            $category->categoryable_id == $userid &&
            $category->categoryable_type == $type
        ) {

            $subcategory->update($data);
            return true;
        }

        return false;
    }
    public function all_total_prices_fo_all_subcategories($user_id, $type)
    {
        if ($type != 'dentist') {
            return 'ليس طبيب';
        }

        $startLastMonth = Carbon::now()->subMonth()->startOfMonth();
        $endLastMonth = Carbon::now()->subMonth()->endOfMonth();

        $totalSum = ItemHistory::whereHas('item', function ($query) use ($user_id) {
            $query->where('creatorable_id', $user_id)
                ->where('creatorable_type', 'dentist')
                ->where('is_static', 1)
            ;
        })
            ->whereBetween('created_at', [$startLastMonth, $endLastMonth])
            ->sum('total_price');
        if ($totalSum == 0) {
            return 'ليس لديك كميات مواد';
        }
        return $totalSum;
    }
    public function sub_categories_and_total_prices($userid, $type)
    {

        $resultsArray = [];

        $now = Carbon::now();

        // تحديد بداية ونهاية الشهر الحالي
        $startOfCurrentMonth = $now->copy()->startOfMonth();

        // تحديد بداية ونهاية الشهر الماضي
        $startOfLastMonth = $startOfCurrentMonth->copy()->subMonth();
        $endOfLastMonth = $startOfCurrentMonth->copy()->subDay();

        $subcategories = Subcategory::whereHas('items', function ($query) use ($userid) {
            $query->where('creatorable_id', $userid)
                ->where('creatorable_type', 'dentist');
        })
            ->with(['items' => function ($query) use ($userid) {
                $query->where('creatorable_id', $userid)
                    ->where('creatorable_type', 'dentist')
                    ->with(['itemHistory']);
            }])->get();

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
