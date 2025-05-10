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
        $perPage = 10;
        $labs = $this->adminService->getLabs($perPage);

        if ($labs->isEmpty()) {
            return $this->returnErrorMessage('لا يوجد مخابر مشتركة بالمنصة.', 404);
        }

        return $this->returnData("subscribed-labs", LabResource::collection($labs), "المخابر المشتركة", 200);
    }


    public function clinics()
    {
        $perPage = 10;
        $clinics = $this->adminService->getClinics($perPage);
        if ($clinics->isEmpty()) {
            return $this->returnErrorMessage('لا يوجد عيادات مشتركة بالمنصة.', 404);
        }

        return $this->returnData("subscribed-clinics", ClinicResource::collection($clinics), "العيادات المشتركة", 200);
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
        $perPage = 10;
        $labs = $this->adminService->fetchLabsWithNullSubscription($perPage);
        if ($labs->isEmpty()) {
            return $this->returnErrorMessage('لا يوجد مخابر غير مجددة اشتراكها بالمنصة.', 404);
        }

        return $this->returnData("subscribed-not-subscribe-labs", LabResource::collection($labs), "المخابر غير مجددة اشتراكها  ", 200);
    }
    public function getClinicsWithNullSubscription()
    {
        $perPage = 10;
        $labs = $this->adminService->getClinicsWithNullSubscription($perPage);
        if ($labs->isEmpty()) {
            return $this->returnErrorMessage('لا يوجد عيادات غير مجددة اشتراكها بالمنصة.', 404);
        }
        return $this->returnData("not-subscribed-clinics", ClinicResource::collection($labs), "العيادات الغير مجددة اشتراكها بالمنصة", 200);
    }
    public function getLabsWithRegisterAcceptedZero()
    {
        $perPage = 10;
        $labs = $this->adminService->getLabsWithRegisterAcceptedZero($perPage);
        if ($labs->isEmpty()) {
            return $this->returnErrorMessage('لا يوجد مخابر مقدمة طلب انضمام للمنصة.', 404);
        }
        return $this->returnData("labs-join-orders", LabResource::collection($labs), " المخابر المقدمة طلب انضمام ", 200);

        //return LabResource::collection($labs);
    }

    public function getClinicsWithRegisterAcceptedZero()
    {
        $perPage = 10;
        $clinics = $this->adminService->getClinicsWithRegisterAcceptedZero($perPage);
        if ($clinics->isEmpty()) {
            return $this->returnErrorMessage('لا يوجد عيادات  مقدمة طلب انضمام للمنصة.', 404);
        }
        return $this->returnData("join-orders-clinics", ClinicResource::collection($clinics), "العيادات المقدمة طلب انضمام ", 200);

        //return ClinicResource::collection($clinics);
    }
    public function renewLabSubscription(RenewSubscriptionLabRequest $request)
    {
        $request->validated($request->all());

        $this->adminService->renewSubscription($request->lab_id, $request->months, $request->subscription_value);
        return $this->returnData("renew-subsicribtion", "", " 'تم تجديد الاشتراك", 200);
    }
    public function renewSubscription_of_clinic(RenewSubscriptionClinicRequest $request)
    {
        $request->validated($request->all());
        $this->adminService->renewClinicSubscription($request->dentist_id, $request->months, $request->subscription_value);
        // return response()->json(['message' => 'تم تجديد الاشتراك']);
        return $this->returnData("renew-subsicribtion", "", " 'تم تجديد الاشتراك", 200);
    }
    public function updateRegisterAccepted($id)
    {
        $labManager = $this->adminService->updateRegisterAccepted($id);

        if ($labManager) {
            return $this->returnData("accept-register", "", "تم قبول طلب الانضمام ", 200);
        }
        return $this->returnData("not-found-lab", "", "المخبر غير موجود", 404);
    }
    public function updateRegisterAcceptedclinic($id)
    {
        $clinic = $this->adminService->updateRegisterAcceptedclinic($id);

        if ($clinic) {
            // return response()->json(['message' => 'Updated successfully', 'data' => $labManager]);
            return $this->returnData("accept-register-clinic", "", "  تم قبول طلب انضمام العيادة ", 200);
        }
        // return response()->json(['message' => 'LabManager not found'], 404);
        return $this->returnData("not-found-clinic", "", "العيادة غير موجود", 404);
    }
}
