<?php

namespace App\Services;

// use App\Http\Resources\subcategoryResource;
use app\Traits\handleResponseTrait;
use App\Http\Resources\itemhistoryresource;
use App\Repositories\itemhistoryRepository;

class ItemhistoryService
{
    use handleResponseTrait;
    protected $itemhistoryRepository;

    public function __construct(itemhistoryRepository $itemhistoryRepository)
    {
        $this->itemhistoryRepository = $itemhistoryRepository;
    }

    public function additemhistory($item_id, array $data)
    {


        $user = auth()->user(); // المستخدم الحالي بعد تحديد Guard بواسطة Middleware
        $type = $user->getMorphClass();
        $check = $this->itemhistoryRepository->Verify_permission_to_add_itemhistory($item_id, $user->id, $type);
        if ($data['quantity'] > 0) {
            $data['total_price'] = $data['quantity'] * $data['unit_price'];
        } else {
            $data['total_price'] = 0;
        }
        if ($check) {
            $result = $this->itemhistoryRepository->addItemhistory($item_id, $data);
            if ($result) {
                return $this->returnSuccessMessage(200, 'تم اضافة الكمية  ');
            }
            return $this->returnErrorMessage(' حدث خطأ اثناءاضافة الكمية .', 404);
        }
        return $this->returnErrorMessage(' انت غير مخول لاضافة الكمية', 404);
    }
    public function itemhistories($itemId)
    {
        // يمكن إضافة additional logic هنا إذا أردت
        $user = auth()->user(); // المستخدم الحالي بعد تحديد Guard بواسطة Middleware
        $type = $user->getMorphClass();
        $itemhistoryRepositories = $this->itemhistoryRepository->itemhistories($itemId, $user->id, $type);
        if ($itemhistoryRepositories->isEmpty()) {

            return $this->returnErrorMessage('لا يوجد كميات ', 404);
        }
        // return $categories;
        return $this->returnData("items", $itemhistoryRepositories, "   كميات المواد", 200);
    }

    public function Repeated_item_histories()
    {
        $user = auth()->user(); // المستخدم الحالي بعد تحديد Guard بواسطة Middleware
        $type = $user->getMorphClass();
        $itemhistoryRepositories = $this->itemhistoryRepository->Repeated_item_histories($user->id, $type);
        if ($itemhistoryRepositories === 'ليس طبيب') {
            return $this->returnErrorMessage('حدث خطأ  انت لست طبيب  ', 500);
        }
        if ($itemhistoryRepositories->isEmpty()) {

            return $this->returnErrorMessage(' لا يوجد كميات متكررة ', 404);
        }
        return $this->returnData("Rpeated_items", itemhistoryresource::collection($itemhistoryRepositories), " كميات المواد المتكررة", 200);
    }
    public function Non_Repeated_item_histories()
    {
        $user = auth()->user(); // المستخدم الحالي بعد تحديد Guard بواسطة Middleware
        $type = $user->getMorphClass();
        $itemhistoryRepositories = $this->itemhistoryRepository->Non_Repeated_item_histories($user->id, $type);
        if ($itemhistoryRepositories === 'ليس طبيب') {
            return $this->returnErrorMessage('حدث خطأ  انت لست طبيب  ', 500);
        }
        if ($itemhistoryRepositories->isEmpty()) {

            return $this->returnErrorMessage(' لا يوجد كميات متكررة ', 404);
        }
        return $this->returnData("Non_Rpeated_items", itemhistoryresource::collection($itemhistoryRepositories), " كميات المواد النادرة", 200);
    }
    public function add_nonrepeated_itemhistory(array $data)
    {


        $user = auth()->user(); // المستخدم الحالي بعد تحديد Guard بواسطة Middleware
        $type = $user->getMorphClass();


        $result = $this->itemhistoryRepository->add_nonrepeated_itemhistory($user->id, $type, $data);
        if ($result === 'ليس طبيب') {
            return $this->returnErrorMessage('حدث خطأانت لست طبيب ', 500);
        }
        if ($result === 'اسم العنصر موجود بالفعل.') {
            return $this->returnSuccessMessage(200, 'تم اضافة الكمية للمادة المدخلة  ');
        }
        if ($result === 'تم') {
            return $this->returnSuccessMessage(200, 'تم اضافة المادة نادرة الشراء  ');
        }
        return $this->returnErrorMessage(' حدث خطأ اثناءاضافة الكمية .', 404);
    }
    // return $this->returnErrorMessage(' انت غير مخول لاضافة الكمية', 404);


}
