<?php
namespace App\Services;

// use App\Http\Resources\subcategoryResource;
use App\Http\Resources\itemResource;
use App\Repositories\ItemRepository;
use app\Traits\handleResponseTrait;

class ItemService
{
    use handleResponseTrait;
    protected $itemRepository;

    public function __construct(ItemRepository $itemRepository)
    {
        $this->itemRepository = $itemRepository;
    }

    public function getItemsForCategory($subcategoryId)
    {
        // يمكن إضافة additional logic هنا إذا أردت
        $user = auth()->user(); // المستخدم الحالي بعد تحديد Guard بواسطة Middleware
        $type = $user->getMorphClass();
        $itemRepositories= $this->itemRepository->getItemsBySubcategory($subcategoryId,$user->id, $type);
         if ($itemRepositories->isEmpty()){

            return $this->returnErrorMessage('لا يوجد مواد', 404);
        }
        // return $categories;
        return $this->returnData("items",itemResource::collection($itemRepositories) , "   المواد", 200);
    }
    public function additem($subcategory_id,array $data)
    {


            $user = auth()->user(); // المستخدم الحالي بعد تحديد Guard بواسطة Middleware
            $type = $user->getMorphClass();
            $check = $this->itemRepository->Verify_permission_to_add_item($subcategory_id,$user->id,$type);

            if ($check) {
                $result=$this->itemRepository->addItem($subcategory_id,$data,$user->id,$type);
            if($result){
                return $this->returnSuccessMessage(200, 'تم اضافة  المادة ');
            }
            return $this->returnErrorMessage(' حدث خطأ اثناءاضافة المادة  .', 404);
            }
            return $this->returnErrorMessage('  انت غير مخول لاضافة المادة  لهذه الفئة الفرعية ', 404);


    }

    public function removeItem($id)
    {

            $user = auth()->user(); // المستخدم الحالي بعد تحديد Guard بواسطة Middleware
            $type = $user->getMorphClass();
            $fromrepo = $this->itemRepository->deleteItem($user->id,$type, $id);
        if ($fromrepo) {
                return $this->returnSuccessMessage(200, 'تم حذف  المادة  بنجاح. ');
            }
        return $this->returnErrorMessage('لم يتم حذف المادة  لانها غير موجودة او انك غير مخول ', 404);
    }

    public function updateItem($id, $data)
    {

            $user = auth()->user(); // المستخدم الحالي بعد تحديد Guard بواسطة Middleware
            $type = $user->getMorphClass();
            $item = $this->itemRepository->findItemById($id);

            if (!$item) {
                return $this->returnErrorMessage(' المادة  غير موجودة.', 404);
            }
            // تحديث البيانات
            $res=$this->itemRepository->updateItem($item, $data,$user->id, $type);
            if ($res){
                 return $this->returnSuccessMessage(200, ' تم تعديل  المادة   ');
            }


            return $this->returnErrorMessage(200, ' لم يتم تعديل المادة لانك غير مخول  ',404);


    }
}
