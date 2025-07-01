<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Services\PatientService;
use Illuminate\Http\JsonResponse;
use app\Traits\handleResponseTrait;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;

class PatientController extends Controller
{
    use handleResponseTrait;
    protected $patientService;

    public function __construct(PatientService $patientService)
    {
        $this->patientService = $patientService;
    }


    public function AllPatients(Request $request): JsonResponse
    {

        try {
            $patients = $this->patientService->getAllWithFilter($request);
            if (!$patients) {
                return $this->returnErrorMessage('المريض غير موجود', 200);
            }
            return $this->returnData('patients', $patients, 'بيانات كل المرضى', 200);
        } catch (Exception $e) {
            return $this->returnErrorMessage($e->getMessage(), '500');
        }
    }


    public function showPatient($patientId): JsonResponse
    {

        try {
            $patient = $this->patientService->showPatient($patientId);
            return $this->returnData('patient_details', $patient, 'معلومات المريض ', 200);
        } catch (Exception $e) {
            return $this->returnErrorMessage('500', $e->getMessage());
        }
    }


    public function storePatient(StorePatientRequest $request): JsonResponse
    {
        // return Auth::id();
        try {
            $patient = $this->patientService->createPatient($request->validated());
            return $this->returnSuccessMessage(201, 'تمت إضافة المريض بنجاح');
        } catch (Exception $e) {
            return $this->returnErrorMessage('500', $e->getMessage());
        }
    }
    public function deletePatient($patientId)
    {
        try {
            $patient = $this->patientService->deletePatient($patientId);
            return $this->returnSuccessMessage(201, 'تم حذف المريض بنجاح');
        } catch (Exception $e) {
            Log::error("error in delete patient", $e->getMessage());
        }
    }

    public function updatePatient($patientId, UpdatePatientRequest $request)
    {

        try {
            $patient = $this->patientService->updatePatient($patientId, $request->validated());

            return $this->returnSuccessMessage(201, 'تم تعديل معلومات المريض بنجاح');
        } catch (Exception $e) {
            Log::error("error in update patient", $e->getMessage());
        }
    }

    public function download_patient_image($file_id)
    {
        $data = $this->patientService->download_patient_image($file_id);

        return $data;
    }
    public function add_patient_image($patient_id, Request $request)
    {
        $data = $this->patientService->add_patient_image($patient_id, $request);

        return $data;
    }
}
