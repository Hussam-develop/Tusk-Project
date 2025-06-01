<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\Subcategory;

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
}
