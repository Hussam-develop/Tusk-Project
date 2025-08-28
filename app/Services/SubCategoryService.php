<?php

namespace App\Services;

use App\Http\Resources\SubcategoryResource;
use App\Repositories\SubCategoryRepository;
use app\Traits\HandleResponseTrait;

class SubCategoryService
{
    use HandleResponseTrait;
    protected $subCategoryRepository;

    public function __construct(SubCategoryRepository $subCategoryRepository)
    {
        $this->subCategoryRepository = $subCategoryRepository;
    }

    public function getSubcategoriesForCategory($categoryId)
    {
        // يمكن إضافة additional logic هنا إذا أردت
        // $user = auth()->user(); // المستخدم الحالي بعد تحديد Guard بواسطة Middleware
        // $type = $user->getMorphClass();
        $subCategoryRepositories = $this->subCategoryRepository->getSubcategoriesByCategoryId($categoryId);
        if ($subCategoryRepositories->isEmpty()) {

            return $this->returnErrorMessage('لا يوجد اصناف فرعية', 200);
        }
        // return $categories;
        return $this->returnData("subCategoryRepositories", SubcategoryResource::collection($subCategoryRepositories), "  الاصناف الفرعية", 200);
    }
    public function removeSubCategory($id)
    {

        // $user = auth()->user(); // المستخدم الحالي بعد تحديد Guard بواسطة Middleware
        // $type = $user->getMorphClass();
        $fromrepo = $this->subCategoryRepository->deleteSubCategory($id);
        if ($fromrepo) {
            return $this->returnSuccessMessage(200, 'تم حذف الفئة الفرعية  بنجاح. ');
        }
        return $this->returnErrorMessage('لم يتم حذف الفئة الفرعية لانها غير موجودة او انك غير مخول ', 200);
    }
    public function addsubcategory($category_id, array $data)
    {


        // $user = auth()->user(); // المستخدم الحالي بعد تحديد Guard بواسطة Middleware
        // $type = $user->getMorphClass();
        // $check = $this->subCategoryRepository->Verify_permission_to_add_subcategory($category_id, $user->id, $type);

        // if ($check) {
        $result = $this->subCategoryRepository->addSubCategory($category_id, $data);
        if ($result) {
            return $this->returnSuccessMessage(200, 'تم اضافة الفئة الفرعية ');
        }
        return $this->returnErrorMessage(' حدث خطأ اثناءاضافة الفئة الفرعية .', 200);
        // }
        // return $this->returnErrorMessage(' انت غير مخول لاضافة فئة فرعية لهذه الفئة', 200);
    }

    public function updateSubCategory($id, $data)
    {

        // $user = auth()->user(); // المستخدم الحالي بعد تحديد Guard بواسطة Middleware
        // $type = $user->getMorphClass();
        $subcategory = $this->subCategoryRepository->findSubCategoryById($id);

        if (!$subcategory) {
            return $this->returnErrorMessage('الصنف الفرعي  غير موجود.', 200);
        }
        // تحديث البيانات
        $res = $this->subCategoryRepository->updateSubCategory($subcategory, $data);
        if ($res) {
            return $this->returnSuccessMessage(200, ' تم تعديل  الصنف الفرعي  ');
        }


        return $this->returnErrorMessage(200, ' لم يتم تعديل  الصنف الفرعي لانك غير مخول  ', 200);
    }
    public function sub_categories_statistics()
    {
        $user = auth()->user(); // المستخدم الحالي بعد تحديد Guard بواسطة Middleware
        $type = $user->getMorphClass();
        $result1 = $this->subCategoryRepository->all_total_prices_fo_all_subcategories($user->id, $type);

        if ($result1 == 'ليس لديك كميات مواد') {
            return $this->returnErrorMessage('ليس لديك كميات مواد', 500);
        }
        $result2 = $this->subCategoryRepository->sub_categories_and_total_prices($user->id, $type);
        foreach ($result2 as &$item) {
            if ($result1 != 0) {
                $item['percentage'] = number_format(($item['total_price_last_month'] * 100) / $result1, 2);
            } else {
                $item['percentage'] = 0; // لتجنب القسمة على صفر
            }
        }

        // return $result2; // أو بأي طريقة تريد إرجاعها بعد إضافة النسب
        return $this->returnData("statistics of subcategories", $result2, "احصائيات الفئات الفرعية", 200);
    }
}
