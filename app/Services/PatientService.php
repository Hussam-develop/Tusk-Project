<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\PatientImage;
use app\Traits\HandleResponseTrait;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Repositories\PatientRepository;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Auth\MailController;



class PatientService
{
    use HandleResponseTrait;

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

    public function  download_patient_image($file_id)
    {
        $file = PatientImage::find($file_id);
        if ($file) {
            $file_path = public_path('project-files/patients-images/' . $file->name);
            return Response::download($file_path, $file->name);
        } else
            return $this->returnErrorMessage("الصورة غير موجودة", 200);
    }

    public function  add_patient_image($patient_id, $request)
    {
        $patient = Patient::find($patient_id);
        $files = $request->file('images');

        if ($files !== null) {

            foreach ($files as $file) {

                $filename =  $file->getClientOriginalName();

                $file_name_existed = PatientImage::where('name', $filename)->exists();
                if ($file_name_existed) {
                    return $this->returnErrorMessage("أعد تسمية الصورة  " . $filename . " رجاءً وحاول مجدداً",  422);
                }

                $image = PatientImage::create([
                    'name' => $filename,
                    'patient_id' => $patient->id
                ]);

                $file->move(public_path("project-files/patients-images"), $filename);
            }
        }
        return $this->returnSuccessMessage(200, "تم إضافة الصور بنجاح");
    }
}
