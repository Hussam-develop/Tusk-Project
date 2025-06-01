<?php

namespace App\Services;

use App\Http\Resources\subcategoryResource;
use App\Repositories\SubCategoryRepository;
use app\Traits\handleResponseTrait;

class SubCategoryService
{
    use handleResponseTrait;
    protected $subCategoryRepository;

    public function __construct(SubCategoryRepository $subCategoryRepository)
    {
        $this->subCategoryRepository = $subCategoryRepository;
    }

    public function getSubcategoriesForCategory($categoryId)
    {
        // يمكن إضافة additional logic هنا إذا أردت
        $user = auth()->user(); // المستخدم الحالي بعد تحديد Guard بواسطة Middleware
        $type = $user->getMorphClass();
        $subCategoryRepositories = $this->subCategoryRepository->getSubcategoriesByCategoryId($categoryId, $user->id, $type);
        if ($subCategoryRepositories->isEmpty()) {

            return $this->returnErrorMessage('لا يوجد اصناف فرعية', 404);
        }
        // return $categories;
        return $this->returnData("subCategoryRepositories", subcategoryResource::collection($subCategoryRepositories), "  الاصناف الفرعية", 200);
    }
    public function removeSubCategory($id)
    {

        $user = auth()->user(); // المستخدم الحالي بعد تحديد Guard بواسطة Middleware
        $type = $user->getMorphClass();
        $fromrepo = $this->subCategoryRepository->deleteSubCategory($user->id, $type, $id);
        if ($fromrepo) {
            return $this->returnSuccessMessage(200, 'تم حذف الفئة الفرعية  بنجاح. ');
        }
        return $this->returnErrorMessage('لم يتم حذف الفئة الفرعية لانها غير موجودة او انك غير مخول ', 404);
    }
    public function addsubcategory($category_id, array $data)
    {


        $user = auth()->user(); // المستخدم الحالي بعد تحديد Guard بواسطة Middleware
        $type = $user->getMorphClass();
        $check = $this->subCategoryRepository->Verify_permission_to_add_subcategory($category_id, $user->id, $type);

        if ($check) {
            $result = $this->subCategoryRepository->addSubCategory($category_id, $data);
            if ($result) {
                return $this->returnSuccessMessage(200, 'تم اضافة الفئة الفرعية ');
            }
            return $this->returnErrorMessage(' حدث خطأ اثناءاضافة الفئة الفرعية .', 404);
        }
        return $this->returnErrorMessage(' انت غير مخول لاضافة فئة فرعية لهذه الفئة', 404);
    }

    public function updateSubCategory($id, $data)
    {

        $user = auth()->user(); // المستخدم الحالي بعد تحديد Guard بواسطة Middleware
        $type = $user->getMorphClass();
        $subcategory = $this->subCategoryRepository->findSubCategoryById($id);

        if (!$subcategory) {
            return $this->returnErrorMessage('الصنف الفرعي  غير موجود.', 404);
        }
        // تحديث البيانات
        $res = $this->subCategoryRepository->updateSubCategory($subcategory, $data, $user->id, $type);
        if ($res) {
            return $this->returnSuccessMessage(200, ' تم تعديل  الصنف الفرعي  ');
        }


        return $this->returnErrorMessage(200, ' لم يتم تعديل  الصنف الفرعي لانك غير مخول  ', 404);
    }
}
