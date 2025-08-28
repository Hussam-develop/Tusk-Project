<?php

namespace App\Repositories;

use App\Models\Dentist;
use App\Models\Patient;
use App\Models\Treatment;
use App\Models\LabManager;
use App\Models\TreatmentImage;
use Illuminate\Support\Facades\DB;
use app\Traits\HandleResponseTrait;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\TreatmentResource;

class TreatmentRepository
{
    use HandleResponseTrait;

    public function show_patient_treatments($dentist_id, $patient_id)
    {
        $patient_treatments = Treatment::where('dentist_id', $dentist_id)
            ->where('patient_id', $patient_id)
            ->orderBy('id', 'desc')
            ->get();
        return TreatmentResource::collection(
            $patient_treatments
        );
    }
    public function show_patient_treatments_non_paid($dentist_id, $patient_id)
    {
        $patient_treatments_non_paid = Treatment::where('dentist_id', $dentist_id)
            ->where('patient_id', $patient_id)
            ->where('is_paid', 0)
            ->get();
        return
            $patient_treatments_non_paid;
    }

    public function add_treatment($request)
    {
        $treatment = Treatment::create([

            'patient_id' => $request->patient_id,
            // 'patient_id' => $request["patient_id"],
            'dentist_id' => Auth::user()->id,
            'medical_case_id' => null,
            'cost' => $request->cost,
            // 'title' => $request->title,
            'type' => $request->type,
            'details' => $request->details,
            'date' => $request->date,
            'is_paid' => false
        ]);

        $patient = Patient::findOrFail($request->patient_id);
        $patient->current_balance -= $request->cost;
        $patient->save();

        $files = $request->file('images');
        // $files = $request['images'];

        if ($files !== null) {

            foreach ($files as $file) {

                // $filename =  $file->getClientOriginalName();
                $filename = (string) date('Y_m_d_H_i_s_') . $file->getClientOriginalName();
                $file_name_existed = TreatmentImage::where('name', $filename)->exists();
                if ($file_name_existed) {
                    $treatment->delete();
                    $patient->current_balance += $request->cost;
                    $patient->save();
                    return $this->returnErrorMessage("أعد تسمية الصورة  " . $filename . " رجاءً وحاول مجدداً",  422);
                }

                $image = TreatmentImage::create([
                    'name' => $filename,
                    'is_diagram' => false,
                    'treatment_id' => $treatment->id
                ]);

                $file->move(public_path("project-files"), $filename);
            }
        }

        $treatment_screenshot = $request->file('treatment_screenshot');
        if ($treatment_screenshot !== null) {

            // $screenshot_image_name =  $treatment_screenshot->getClientOriginalName();
            $screenshot_image_name = (string) date('Y_m_d_H_i_s_') . $treatment_screenshot->getClientOriginalName();
            $file_name_existed = TreatmentImage::where('name', (string) date('Y_m_d_H_i_s_') . $treatment_screenshot)->exists();
            if ($file_name_existed) {
                $treatment->delete();
                $patient->current_balance += $request->cost;
                $patient->save();
                // return $this->returnErrorMessage("Rename the file" . $screenshot_image_name . "please," . "the File name is already taken", 200);
                return $this->returnErrorMessage("أعد تسمية الصورة  " . $screenshot_image_name . " رجاءً وحاول مجدداً",  422);
            }

            $screenshot_image = TreatmentImage::create([
                'name' => $screenshot_image_name,
                'is_diagram' => true,
                'treatment_id' => $treatment->id
            ]);
            $treatment_screenshot->move(public_path("project-files"), $screenshot_image_name);
        }
        return $this->returnSuccessMessage(200, "تمّ إضافة الجلسة بنجاح");
    }
    public function update_treatment($treatment_id, $request)
    {
        $patient = Patient::findOrFail($request->patient_id);
        $treatment = treatment::findOrFail($treatment_id);

        if ($treatment->cost !== $request->cost) {

            $patient->current_balance += $treatment->cost;
            $patient->current_balance -= $request->cost;
            $patient->save();
        }

        $treatment->update($request->all());
        $treatment->save();

        return $this->returnSuccessMessage(200, "تمّ تعديل بيانات الجلسة بنجاح");
    }
    public function get_labs_by_labtype($lab_type)
    {
        $labs = LabManager::where("lab_type", $lab_type)->get("lab_name");
        if ($labs->isEmpty()) {
            return $this->returnErrorMessage("لا توجد مخابر من النوع $lab_type ",  200);
        }
        return $this->returnData("labs", $labs, "المخابر من النوع $lab_type", 200);
    }

    //////////////////////////////////////////////////////// Statistics
    public function treatments_statistics($user_id, $type)
    {

        if ($type != "dentist") {
            return 'ليس طبيب';
        }

        $dentist = Dentist::where('id', $user_id)->first();
        $treatments = $dentist->treatments;
        if ($treatments->isEmpty()) {
            return 'ليس لديك معالجات';
        }



        // استعلام يستخدم نموذج Treatment
        $results = Treatment::select(
            DB::raw('MONTH(date) as month'),    // استخراج رقم الشهر من التاريخ
            'type',                              // نوع العلاج
            DB::raw('COUNT(*) as total')         // حساب العدد
        )
            ->groupBy('month', 'type')              // التجميع حسب الشهر والنوع
            ->orderBy('month')                      // ترتيب النتائج حسب الشهر
            ->get();

        // تنظيم النتائج في مصفوفة مرتبة
        $formattedResults = [];

        foreach ($results as $result) {
            $formattedResults[$result->month][$result->type] = $result->total;
        }
        // dd($formattedResults);
        return $formattedResults;
    }

    ///////////////////////////////////////////////////////
    public function getAll()
    {
        return Treatment::all();
    }

    public function getById($id)
    {
        return Treatment::findOrFail($id);
    }

    public function getPaginate($perPage = 10)
    {
        return Treatment::paginate($perPage);
    }

    public function create(array $data)
    {
        return Treatment::create($data);
    }

    public function update($id, array $data)
    {
        $item = Treatment::findOrFail($id);
        $item->update($data);
        return $item;
    }

    public function delete($id)
    {
        return Treatment::destroy($id);
    }
}
