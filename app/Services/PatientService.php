<?php

namespace App\Services;

use App\Http\Controllers\Auth\MailController;

use app\Traits\handleResponseTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Repositories\PatientRepository;



class PatientService
{
    use handleResponseTrait;

    public function __construct(protected PatientRepository $patientRepository)
    {
        $this->patientRepository = $patientRepository;
    }
    public function paitents_statistics()
    {
        $user = auth()->user(); // المستخدم الحالي بعد تحديد Guard بواسطة Middleware
        $type = $user->getMorphClass();
        $result = $this->patientRepository->paitents_statistic($user->id, $type);
        if (!$result) {
            return $this->returnErrorMessage('حدث خطأ  انت لست طبيب  ', 500);
        }
        if ($result === 'لا مرضى') {
            return $this->returnErrorMessage('ليس لديك مرضى', 500);
        }


        return $this->returnData("paitents_statistic", $result, " احصائيات المرضى ", 200);
    }
    public function getAllPatientswith($perPage)
    {
        return $this->patientRepository->getAllWithPaginate($perPage);
    }


    public function getAllWithFilter($request)
    {
        //return $request->full_name;

        return $this->patientRepository->getAllWithFilter($request);
    }


    public function createPatient(array $data)
    {
        return $this->patientRepository->store($data);
    }


    public function deletePatient($id)
    {
        return $this->patientRepository->delete($id);
    }

    public function showPatient($id)
    {
        return $this->patientRepository->show($id);
    }

    public function updatePatient($id, $request)
    {
        $updatedData = $this->patientRepository->update($id, $request);
        return $updatedData;
    }
}
