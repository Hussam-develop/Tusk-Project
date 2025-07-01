<?php

namespace App\Services;

use App\Repositories\CategoryRepository;
use app\Traits\handleResponseTrait;

class CategoryService
{
    use handleResponseTrait;

    protected $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
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
        if ($category && $category->categoryable_id == $user->id && $category->categoryable_type == $type) {

            // تحديث البيانات
            $this->categoryRepository->updateCategory($category, $data);

            return $this->returnSuccessMessage(200, 'تم تعديل  الصنف ');
        }
        return $this->returnErrorMessage(200, 'لم يتم تعديل  الصنف لأنك غير مخول  ', 200);
    }
}
