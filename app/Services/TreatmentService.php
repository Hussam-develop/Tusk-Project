<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\Treatment;
use App\Models\TreatmentImage;
use app\Traits\handleResponseTrait;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\TreatmentRequest;
use Illuminate\Support\Facades\Response;
use App\Repositories\TreatmentRepository;
use Illuminate\Http\Request;

class TreatmentService
{
    use handleResponseTrait;

    protected $repository;

    public function __construct(TreatmentRepository $repository)
    {
        $this->repository = $repository;
    }
    public function show_patient_treatments($patient_id)
    {
        $dentist = Auth::user();
        $patient_treatments =  $this->repository->show_patient_treatments($dentist->id, $patient_id);
        return $patient_treatments;
    }
    public function show_treatment_details($treatment_id)
    {
        $treatment =  $this->repository->getById($treatment_id);
        return $treatment->load('images');
    }
    public function add_treatment(TreatmentRequest $request)
    {
        $treatment =  $this->repository->add_treatment($request);
        return $treatment;
    }
    public function update_treatment($treatment_id, TreatmentRequest $request)
    {
        $treatment =  $this->repository->update_treatment($treatment_id, $request);
        return $treatment;
    }
    public function download_treatment_image($file_id)
    {

        $file = TreatmentImage::find($file_id);
        if ($file) {
            $file_path = public_path('project-files/' . $file->name);
            return Response::download($file_path, $file->name);
        } else
            return $this->returnErrorMessage("الصورة غير موجودة", 200);
    }
    public function  add_treatment_image($treatment_id, Request $request)
    {

        $traetment = Treatment::find($treatment_id);
        $files = $request->file('images');

        if ($files !== null) {

            foreach ($files as $file) {

                $filename = (string) date('Y_m_d_H_i_s_') . $file->getClientOriginalName();

                // $file_name_existed = TreatmentImage::where('name', $filename)->exists();
                // if ($file_name_existed) {
                //     return $this->returnErrorMessage("أعد تسمية الصورة  " . $filename . " رجاءً وحاول مجدداً",  422);
                // }

                $image = TreatmentImage::create([
                    'name' => $filename,
                    'is_diagram' => false,
                    'treatment_id' => $traetment->id
                ]);

                $file->move(public_path("project-files"), $filename);
            }
        }
        return $this->returnSuccessMessage(200, "تم إضافة الصور بنجاح");
    }

    /////////////////////////////////////////////////////////////////
    public function treatments_statistics()
    {
        $user = auth()->user(); // المستخدم الحالي بعد تحديد Guard بواسطة Middleware
        $type = $user->getMorphClass();
        $result = $this->repository->treatments_statistics($user->id, $type);
        // return $result;
        if ($result == 'ليس طبيب') {
            return $this->returnErrorMessage('حدث خطأ  انت لست طبيب  ', 500);
        }

        if ($result == 'ليس لديك معالجات') {
            return $this->returnErrorMessage('ليس لديك معالجات', 500);
        }
        return $this->returnData("treatments_statistics", $result, " احصائيات المعالجات ", 200);
    }
    /////////////////////////////////////////////////////////////////
    public function getAll()
    {
        return $this->repository->getAll();
    }

    public function getById($id)
    {
        return $this->repository->getById($id);
    }

    public function getPaginate($perPage = 10)
    {
        return $this->repository->getPaginate($perPage);
    }

    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    public function update($id, array $data)
    {
        return $this->repository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }
}
