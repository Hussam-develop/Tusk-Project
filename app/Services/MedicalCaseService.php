<?php

namespace App\Services;

use App\Models\File;
use App\Models\MedicalCase;
use Illuminate\Http\Request;
use app\Traits\handleResponseTrait;
use Illuminate\Support\Facades\Response;
use App\Repositories\MedicalCaseRepository;

class MedicalCaseService
{
    use handleResponseTrait;

    protected $repository;

    public function __construct(MedicalCaseRepository $repository)
    {
        $this->repository = $repository;
    }
    public function get_labs_by_labtype($lab_type)
    {
        $labs =  $this->repository->get_labs_by_labtype($lab_type);
        return $labs;
    }
    public function show_lab_cases_as_doctor($lab_id)
    {
        $medical_cases =  $this->repository->show_lab_cases_as_doctor($lab_id);
        if ($medical_cases->isEmpty()) {
            return $this->returnErrorMessage("لا توجد  حالات لهذا المخبر بعد ",  200);
        }
        return $this->returnData("medical_cases", $medical_cases, "الحالات", 200);
    }
    public function get_medical_case_details($medical_case_id)
    {
        $medical_case =  $this->repository->getById($medical_case_id);
        if ($medical_case) {
            return $this->returnData("medical_case", $medical_case, "تفاصيل الحالة", 200);
        }
        return $this->returnErrorMessage("لم يتم إيجاد الحالة المرضية هذه ",  200);
    }
    public function add_medical_case_to_lab($request)
    {
        $medical_case =  $this->repository->create($request);
        $addFilesToCase =  $this->add_case_images_with_screenshot($medical_case->id, $request);
        if ($medical_case) {
            return $this->returnSuccessMessage(201, "تم إنشاء الحالة بنجاح وإرسالها للمخبر");
        }
        return $this->returnErrorMessage("حدث خطأ ما الرجاء إعادة إرسال الحالة مرة أخرى للمخبر",  200);
    }
    public function delete_request($medical_case_id)
    {
        $medical_case =  $this->repository->getById($medical_case_id);
        // dd($medical_case["medical_case_details"]["status"]);
        // dd(in_array($medical_case["medical_case_details"]["status"], [1, 2, 3, 4, 5]));
        if ($medical_case["medical_case_details"]["status"] == 1/*"ordered(pending)"*/) {
            $medical_case["medical_case_details"]->delete();
            return $this->returnSuccessMessage(200, "تم إلغاء إرسال الحالة للمخبر بنجاح");
        }
        if (in_array($medical_case["medical_case_details"]["status"], [2, 3, 4, 5])) {
            $medical_case["medical_case_details"]->delete();
            return $this->returnErrorMessage("لم يتم حذف الحالة المرضية لأن مدير مدير المخبر قد قبل الحالة . يرجى التواصل مع مدير المخبر.",  422);
        }

        return $this->returnErrorMessage("حدث خطأ ما. الرجاء المحاولة مجددا لإلغاء إرسال طلب  الحالة",  422);
    }
    public function confirm_delivery($medical_case_id)
    {
        $medical_case =  $this->repository->getById($medical_case_id);
        if ($medical_case["medical_case_details"]["status"] == 4/*"ready"*/) {
            $this->repository->confirm_delivery($medical_case["medical_case_details"]["id"]);
            return $this->returnSuccessMessage(200, "تم تغيير نوع الحالة إلى مستلمة بنجاح");
        }
        if (in_array($medical_case["medical_case_details"]["status"], [1, 2, 3])) {
            return $this->returnErrorMessage("يوجد خطأ . الحالة ليست جاهزة بعد !",  422);
        }
        if ($medical_case["medical_case_details"]["status"] == 5 && $medical_case["medical_case_details"]["confirm_delivery"] == true) {
            return $this->returnErrorMessage("لم يتم استكمال الطلب . الحالة مستلمة بالفعل !",  422);
        }
        return $this->returnErrorMessage("حدث خطأ ما. الرجاء المحاولة مجدداً لتغيير الحالة إلى مستلمة",  422);
    }
    public function download_medical_case_image($case_id)
    {
        $file = File::find($case_id);
        if ($file) {
            $file_path = public_path('project-files/medical-cases/' . $file->name);
            return Response::download($file_path, $file->name);
        } else
            return $this->returnErrorMessage("الصورة غير موجودة", 404);
    }
    public function  add_case_images_with_screenshot($case_id, Request $request)
    {
        $medicalCase = MedicalCase::where("id", $case_id)->first();
        $files = $request->file('case_images');

        if ($files !== null) {

            foreach ($files as $file) {

                $filename =  $file->getClientOriginalName();

                $file_name_existed = File::where('name', $filename)->exists();
                if ($file_name_existed) {
                    $medicalCase->delete();
                    return $this->returnErrorMessage("أعد تسمية الصورة  " . $filename . " رجاءً وحاول مجدداً",  422);
                }

                $image = File::create([
                    'name' => $filename,
                    'is_case_image' => false,
                    'medical_case_id' => $case_id
                ]);

                $file->move(public_path("project-files/medical-cases"), $filename);
            }
        }

        $case_screenshot = $request->file('case_screenshot');
        if ($case_screenshot !== null) {

            $screenshot_image_name =  $case_screenshot->getClientOriginalName();

            $file_name_existed = File::where('name', $screenshot_image_name)->exists();
            if ($file_name_existed) {
                $medicalCase->delete();
                return $this->returnErrorMessage("أعد تسمية الصورة  " . $screenshot_image_name . " رجاءً وحاول مجدداً",  422);
            }

            $screenshot_image = File::create([
                'name' => $screenshot_image_name,
                'is_case_image' => true,
                'medical_case_id' => $medicalCase->id
            ]);
            $case_screenshot->move(public_path("project-files"), $screenshot_image_name);
        }

        return $this->returnSuccessMessage(200, "تم إضافة الصور بنجاح");
    }
    public function  add_case_images($case_id, Request $request)
    {
        $medicalCase = MedicalCase::where("id", $case_id)->first();
        $files = $request->file('case_images');

        if ($files !== null) {

            foreach ($files as $file) {

                $filename =  $file->getClientOriginalName();

                $file_name_existed = File::where('name', $filename)->exists();
                if ($file_name_existed) {
                    return $this->returnErrorMessage("أعد تسمية الصورة  " . $filename . " رجاءً وحاول مجدداً",  422);
                }

                $image = File::create([
                    'name' => $filename,
                    'is_case_image' => false,
                    'medical_case_id' => $case_id
                ]);

                $file->move(public_path("project-files/medical-cases"), $filename);
            }
        }
        return $this->returnSuccessMessage(200, "تم إضافة الصور بنجاح");
    }
    ////////////////////////////
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

    public function update_case($case_id, $request)
    {
        $medicalCase = MedicalCase::findOrFail($case_id);
        if ($medicalCase) {
            if ($medicalCase->status == 1) {
                $updated_case =   $this->repository->update($case_id, $request);
                return $this->returnSuccessMessage(200, "تم تعديل بيانات الحالة بنجاح");
            }
            return $this->returnErrorMessage(422, "لم يتم التعديل لأن المخبر بدأ بتصنيع الحالة , يرجى التواصل مع مدير المخبر لحذف الحالة");
        }
        return $this->returnErrorMessage("لم يتم إيجاد الحالة المرضية !",  404);
    }

    public function delete($id)
    {
        $medicalCase = MedicalCase::findOrFail($id);
        if ($medicalCase) {
            if ($medicalCase->status == 1) {
                $this->repository->delete($id);
                return $this->returnSuccessMessage(200, "تم حذف الحالة بنجاح");
            }
            return $this->returnErrorMessage("لم يتم حذف الحالة المرضية لأن مدير المخبر قد قبل الحالة !",  422);
        }
        return $this->returnErrorMessage("حدث خطأ ما . لم يتم حذف الحالة المرضية !",  404);
    }
}
