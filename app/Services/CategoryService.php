<?php

namespace App\Services;

use app\Traits\handleResponseTrait;
use App\Repositories\CategoryRepository;
use App\Repositories\SubCategoryRepository;

class CategoryService
{
    use handleResponseTrait;

    protected $categoryRepository;
    protected $subCategoryRepository;

    public function __construct(CategoryRepository $categoryRepository, SubCategoryRepository $subCategoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->subCategoryRepository = $subCategoryRepository;
    }

    public function getCategories()
    {

        $categories = $this->categoryRepository->getCategoriesForCurrentUser();
        if ($categories->isEmpty()) {

            return $this->returnErrorMessage('لا يوجد اصناف', 200);
        }
        // return $categories;
        return $this->returnData("categories", $categories, " الاصناف", 200);
    }
    public function addcategory(array $data)
    {


        $user = auth()->user(); // المستخدم الحالي بعد تحديد Guard بواسطة Middleware
        $type = $user->getMorphClass();
        $fromaddrepo = $this->categoryRepository->create($user->id, $type, $data);

        if ($fromaddrepo) {
            return $this->returnSuccessMessage(200, 'تم إضافة الفئة  بنجاح. ');
        }
    }
    public function removeCategory($id)
    {

        $user = auth()->user(); // المستخدم الحالي بعد تحديد Guard بواسطة Middleware
        $type = $user->getMorphClass();
        $fromrepo = $this->categoryRepository->deleteCategory($user->id, $type, $id);
        if ($fromrepo) {
            return $this->returnSuccessMessage(200, 'تم حذف الفئة  بنجاح. ');
        }
        return $this->returnErrorMessage('لم يتم حذف الفئة لانها غير موجودةاو انك غير مخول ', 200);
    }
    public function updateCategory($id, $data)
    {

        $user = auth()->user(); // المستخدم الحالي بعد تحديد Guard بواسطة Middleware
        $type = $user->getMorphClass();
        $category = $this->categoryRepository->findCategoryById($id);

        if (!$category) {
            return $this->returnErrorMessage('الصنف غير موجودة.', 200);
        }
        if ($category) {

            // تحديث البيانات
            $this->categoryRepository->updateCategory($category, $data);

            return $this->returnSuccessMessage(200, 'تم تعديل  الصنف ');
        }
        return $this->returnErrorMessage(200, 'لم يتم تعديل  الصنف لأنك غير مخول  ', 200);
    }
    public function categories_statistics()
    {
        $user = auth()->user(); // المستخدم الحالي بعد تحديد Guard بواسطة Middleware
        $type = $user->getMorphClass();
        $result1 = $this->categoryRepository->all_total_prices_fo_all_subcategories1($user->id, $type);

        if ($result1 == 'ليس لديك كميات مواد') {
            return $this->returnErrorMessage('ليس لديك كميات مواد', 500);
        }
        $result2 = $this->categoryRepository->sub_categories_and_total_prices1($user->id, $type);
        $result3 = $this->categoryRepository->categories_and_total_prices($result2);
        foreach ($result3 as &$item) {
            if ($result1 != 0) {
                $item['percentage'] = number_format(($item['total_price_last_month'] * 100) / $result1, 2);
            } else {
                $item['percentage'] = 0; // لتجنب القسمة على صفر
            }
        }
        return $this->returnData("statistics of categories", $result3, "احصائيات الفئات ", 200);
    }
}
