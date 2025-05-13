<?php

namespace App\Services;

use App\Http\Resources\secretaryresource;

use App\Repositories\SecretaryRepository;
use app\Traits\handleResponseTrait;
use Illuminate\Support\Facades\Auth;

class SecretaryService
{
    use handleResponseTrait;
    protected $secretaryRepository;

    public function __construct(SecretaryRepository $secretaryRepository)
    {
        $this->secretaryRepository = $secretaryRepository;
    }

    public function getSecretaries()
    {
        try {
            // الحصول على معرف الطبيب المسجل الدخول
            $dentistId = Auth::id();

            // مراجعة السكرتيرات
            $secretaries = $this->secretaryRepository->getSecretariesByDentistId($dentistId);
            if ($secretaries->isEmpty()) {
                return $this->returnErrorMessage('لا يوجد سكرتيرات .', 404);
            }

            return $this->returnData("secretaries", secretaryresource::collection($secretaries), " السكرتيرات ", 200);
        } catch (\Exception $e) {
            return $this->returnErrorMessage('حدث خطأ أثناء جلب السكرتيرات.', 500);
        }
    }
    public function updateSecretary($id, $data)
    {
        try {
            // البحث عن السكرتيرة
            $secretary = $this->secretaryRepository->findSecretaryById($id);

            if (!$secretary) {
                return $this->returnErrorMessage('السكرتيرة غير موجودة.', 404);
            }

            // تحديث البيانات
            $this->secretaryRepository->updateSecretary($secretary, $data);

            // return response()->json(['message' => 'تم تعديل بيانات السكرتيرة بنجاح.'], 200);
            return $this->returnSuccessMessage(200, 'تم تعديل بيانات السكرتيرة ');
        } catch (\Exception $e) {
            return $this->returnErrorMessage(' حدث خطأ اثناء تعديل بيانات السكرتيرة .', 404);
        }
    }
    public function removeSecretary($id)
    {
        $dentistId = Auth::id();
        $secretary = $this->secretaryRepository->findSecretaryById($id);

        // تحقق إذا كانت السكرتيرة مرتبطة بالطبيب
        if ($secretary && $secretary->dentist_id == $dentistId) {
            $secretary->delete();
            return $this->returnSuccessMessage(200, 'تم حذف السكرتيرة  ');
        }
        return $this->returnErrorMessage('  السكرتيرة غير موجودة  .', 404);
    }
}
//
