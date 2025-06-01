<?php

namespace App\Services;

use App\Http\Controllers\Auth\MailController;

use App\Http\Resources\latestAccountInLabResource;
use App\Repositories\LabmangerRepository;
use app\Traits\handleResponseTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LabmangerService
{
    use handleResponseTrait;
    protected $labmangerrepository;

    public function __construct(LabmangerRepository $labmangerrepository)
    {
        $this->labmangerrepository = $labmangerrepository;
    }
    public function get_labs_dentist_joined()
    {
        $user = auth()->user(); // المستخدم الحالي بعد تحديد Guard بواسطة Middleware
        $type = $user->getMorphClass();
        $labs = $this->labmangerrepository->get_labs_dentist_joined($user->id, $type);
        if (!$labs) {
            return $this->returnErrorMessage('حدث خطأ  انت لست طبيب  ', 500);
        }
        if ($labs->isEmpty()) {
            return $this->returnErrorMessage('لست مشترك عند اي مخير ', 500);
        }

        return $this->returnData("labs iam joind", $labs, " مخابري ", 200);
    }
    public function show_account_of_dentist_in_lab($lab_id)
    {
        $user = auth()->user(); // المستخدم الحالي بعد تحديد Guard بواسطة Middleware
        $type = $user->getMorphClass();
        $Account = $this->labmangerrepository->show_account_of_dentist_in_lab($lab_id, $user->id, $type);
        if (is_null($Account)) {
            return $this->returnErrorMessage('ليس لديك سجل حساب هنا', 500);
        }
        // if ($Account->isEmpty()){
        //      return $this->returnErrorMessage('ليس لديك سجلات حسابات ', 500);
        //  }
        if ($Account === 'ليس طبيب') {
            return $this->returnErrorMessage('لست طبيب انت غير مخول', 500);
        }
        return $this->returnData(" Latest Account of this lab ", $Account, " اخر سجل حساب لدي  ", 200);


        //latestAccountInLabResource::collection(
    }
    public function show_all_labs()
    {
        $user = auth()->user(); // المستخدم الحالي بعد تحديد Guard بواسطة Middleware
        $type = $user->getMorphClass();
        $Labs = $this->labmangerrepository->show_all_labs($user->id, $type);
        if ($Labs === 'ليس طبيب') {
            return $this->returnErrorMessage('لست طبيب انت غير مخول', 500);
        }
        if ($Labs->isEmpty()) {
            return $this->returnErrorMessage(' لا يوجد مخابر مسجلة بالمنصة أنت غير مشترك بها ', 500);
        }
        // if ($Account->isEmpty()){
        //      return $this->returnErrorMessage('ليس لديك سجلات حسابات ', 500);
        //  }

        return $this->returnData(" Latest Account of this lab ", $Labs, "المخابر المسجلة في المنصة", 200);
    }
    public function show_lab_not_injoied_details($id)
    {
        $user = auth()->user(); // المستخدم الحالي بعد تحديد Guard بواسطة Middleware
        $type = $user->getMorphClass();
        $Lab_details = $this->labmangerrepository->show_lab_not_injoied_details($id, $user->id, $type);
        if ($Lab_details === 'ليس طبيب') {
            return $this->returnErrorMessage('لست طبيب انت غير مخول', 500);
        }
        if ($Lab_details === 'المخبر غير موجود.') {
            return $this->returnErrorMessage('المخبر غير موجود', 500);
        }

        return $this->returnData(" Lab Details ", $Lab_details, 'تفاصيل المخبر ', 200);
    }
    public function submit_join_request_to_lab($id)
    {
        $user = auth()->user(); // المستخدم الحالي بعد تحديد Guard بواسطة Middleware
        $type = $user->getMorphClass();
        $join_request = $this->labmangerrepository->submit_join_request_to_lab($id, $user->id, $type);
        if ($join_request === 'ليس طبيب') {
            return $this->returnErrorMessage('لست طبيب انت غير مخول', 500);
        }
        if ($join_request === 'لقد أرسلت طلبًا سابقًا.') {
            return $this->returnErrorMessage('لقد أرسلت طلبًا سابقًا.', 500);
        }

        if ($join_request === 'المخبر غير موجود.') {
            return $this->returnErrorMessage('المخبر غير موجود', 500);
        }
        return $this->returnSuccessMessage(200, 'تم إرسال الطلب بنجاح.');
    }
    public function filter_not_join_labs($province = null, $name = null)
    {
        $user = auth()->user(); // المستخدم الحالي بعد تحديد Guard بواسطة Middleware
        $type = $user->getMorphClass();
        $labs = $this->labmangerrepository->filter_not_join_labs($user->id, $type, $province, $name);
        if ($labs === 'ليس طبيب') {
            return $this->returnErrorMessage('لست طبيب انت غير مخول', 500);
        }
        if ($labs->isEmpty()) {
            return $this->returnErrorMessage('لا يوجد مخابر مشابهة للبحث', 500);
        }

        return $this->returnData("filterd labs ", $labs, " المخابر المشابهة للبحث  ", 200);
    }
}
