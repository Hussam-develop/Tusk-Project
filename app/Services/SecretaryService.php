<?php

namespace App\Services;

use app\Traits\handleResponseTrait;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Secretaryresource;
use App\Repositories\SecretaryRepository;
use App\Http\Controllers\Auth\MailController;

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
                return $this->returnErrorMessage('لا يوجد سكرتيرات .', 200);
            }

            return $this->returnData("secretaries", Secretaryresource::collection($secretaries), " السكرتيرات ", 200);
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
                return $this->returnErrorMessage('السكرتيرة غير موجودة.', 200);
            }

            // تحديث البيانات
            $this->secretaryRepository->updateSecretary($secretary, $data);

            // return response()->json(['message' => 'تم تعديل بيانات السكرتيرة بنجاح.'], 200);
            return $this->returnSuccessMessage(200, 'تم تعديل بيانات السكرتيرة ');
        } catch (\Exception $e) {
            return $this->returnErrorMessage(' حدث خطأ اثناء تعديل بيانات السكرتيرة .', 200);
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
        return $this->returnErrorMessage('  السكرتيرة غير موجودة  .', 200);
    }
    public function addSecretary(array $data)
    {

        try {
            $dentistId = Auth::id();
            $fromaddrepo = $this->secretaryRepository->create($dentistId, $data);
            $MailController = new MailController();

            if ($fromaddrepo) {
                $MailController->send_verification_code('secretary', $data['email']);
                return $this->returnSuccessMessage(200, 'تم إضافة السكرتيرة بنجاح. ');
            }
        } catch (\Exception $e) {
            Log::error("Unable to addSecretary," . $e->getMessage());
            return $this->returnErrorMessage('حدث خطأ أثناء اضافة  السكرتيرة.', 500);
        }
    }
}
//
