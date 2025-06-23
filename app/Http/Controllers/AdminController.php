<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AdminService;
use App\Http\Resources\LabResource;
use app\Traits\handleResponseTrait;
use App\Http\Resources\ClinicResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\RenewSubscriptionRequest;
use App\Http\Requests\RenewSubscriptionLabRequest;
use App\Http\Requests\RenewSubscriptionClinicRequest;

class AdminController extends Controller
{
    use handleResponseTrait;
    protected $adminService;


    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    public function labs()
    {
        // $perPage = 10;
        $labs = $this->adminService->getLabs(/*$perPage*/);

        if ($labs->isEmpty()) {
            return $this->returnErrorMessage('لا يوجد مخابر مشتركة بالمنصة.', 404);
        }

        return $this->returnData("subscribed-labs", $labs, "المخابر المشتركة", 200);
    }


    public function clinics()
    {
        // $perPage = 10;
        $clinics = $this->adminService->getClinics(/*$perPage*/);
        if ($clinics->isEmpty()) {
            return $this->returnErrorMessage('لا يوجد عيادات مشتركة بالمنصة.', 404);
        }

        return $this->returnData("subscribed-clinics", $clinics, "العيادات المشتركة", 200);
    }
    public function filterLabs(Request $request)
    {
        $labName = $request->input('lab_name');
        $registerDate = $request->input('register_date');

        // يمكنك الحصول على عدد العناصر في الصفحة كمتحول من الطلب إذا احتجت لذلك
        $perPage = 10; // القيمة الافتراضية 10

        $labs = $this->adminService->filterLabs($labName, $registerDate, $perPage);
        if ($labs->isEmpty()) {
            return $this->returnErrorMessage('لا يوجد مخابر  مشابهة للبحث.', 404);
        }
        return $this->returnData("subscribed-and-filterd-labs", LabResource::collection($labs), "المخابر المشتركة والمفلترة", 200);
    }

    public function filterclinics(Request $request)
    {
        $clinic_name = $request->input('name');
        $register_date = $request->input('register_date');
        $perPage = 10;
        $clinics = $this->adminService->filterclinics($clinic_name, $register_date, $perPage);
        if ($clinics->isEmpty()) {
            return $this->returnErrorMessage('لا يوجد عيادات  مشابهة للبحث.', 404);
        }
        return $this->returnData("subscribed-and-filterd-clinics", ClinicResource::collection($clinics), "  العيادات المشتركة والمفلترة", 200);

        // return ClinicResource::collection($clinics);
    }
    public function getLabsWithNullSubscription()
    {
        // $perPage = 10;
        $labs = $this->adminService->fetchLabsWithNullSubscription(/*$perPage*/);
        if ($labs->isEmpty()) {
            return $this->returnErrorMessage('لا يوجد مخابر غير مجددة اشتراكها بالمنصة.', 404);
        }

        return $this->returnData("non_subscribed_labs", $labs, "المخابر غير المجددة اشتراكها  ", 200);
    }
    public function getClinicsWithNullSubscription()
    {
        // $perPage = 10;
        $clinics = $this->adminService->getClinicsWithNullSubscription(/*$perPage*/);
        if ($clinics->isEmpty()) {
            return $this->returnErrorMessage('لا يوجد أطباء غير مجددين اشتراكهم بالمنصة.', 404);
        }
        return $this->returnData("non_subscribed_clinics", $clinics, "الأطباء غير المجددين اشتراكهم بالمنصة", 200);
    }
    public function getLabsWithRegisterAcceptedZero()
    {
        // $perPage = 10;
        $labs = $this->adminService->getLabsWithRegisterAcceptedZero(/*$perPage*/);
        if ($labs->isEmpty()) {
            return $this->returnErrorMessage('لا يوجد مخابر مقدمة طلب انضمام للمنصة.', 404);
        }
        return $this->returnData("labs_register_requests", $labs, " المخابر المقدمة طلب انضمام ", 200);

        //return LabResource::collection($labs);
    }

    public function getClinicsWithRegisterAcceptedZero()
    {
        // $perPage = 10;
        $clinics = $this->adminService->getClinicsWithRegisterAcceptedZero(/*$perPage*/);
        if ($clinics->isEmpty()) {
            return $this->returnErrorMessage('لا يوجد عيادات  مقدمة طلب انضمام للمنصة.', 404);
        }
        return $this->returnData("clinics_register_requests", $clinics, "العيادات المقدمة طلب انضمام ", 200);

        //return ClinicResource::collection($clinics);
    }
    public function renewSubscription(RenewSubscriptionLabRequest $request)
    {
        $request->validated($request->all());

        $this->adminService->renewSubscription($request->subscription_id, $request->months/*, $request->subscription_value*/);
        return $this->returnSuccessMessage(200,  " تم تجديد الاشتراك بنجاح لمدة " . $request->months . " شهور");
    }

    public function updateRegisterAccepted($id)
    {
        $labManager = $this->adminService->updateRegisterAccepted($id);

        if ($labManager == false) {
            return $this->returnErrorMessage("المخبر تم انضمامه بالفعل ", 422);
        }
        if ($labManager) {
            return $this->returnData("accept-register", "", "تم قبول طلب انضمام المخبر ", 200);
        }
        return $this->returnErrorMessage("حدث خطأ ما أثناء قبول طلب انضمام المخبر ", 422);
    }
    public function updateRegisterAcceptedclinic($id)
    {
        $clinic = $this->adminService->updateRegisterAcceptedclinic($id);
        if ($clinic == false) {
            return $this->returnErrorMessage("الطبيب تم انضمامه بالفعل ", 422);
        }
        if ($clinic) {
            // return response()->json(['message' => 'Updated successfully', 'data' => $labManager]);
            return $this->returnData("accept-register-clinic", "", "  تم قبول طلب انضمام الطبيب ", 200);
        }
        // return response()->json(['message' => 'LabManager not found'], 404);
        return $this->returnErrorMessage("حدث خطأ ما أثناء قبول طلب انضمام الطبيب ", 422);
    }
}
