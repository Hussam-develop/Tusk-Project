<?php

namespace App\Repositories;

use App\Models\File;
use App\Models\Patient;
use App\Models\LabManager;
use App\Models\MedicalCase;
use App\Models\Treatment;
use app\Traits\handleResponseTrait;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MedicalCaseRepository
{
    use handleResponseTrait;
    public function get_labs_by_labtype($lab_type)
    {
        // $labs = LabManager::where("lab_type", $lab_type)->get("lab_name");
        $labs = LabManager::where("lab_type", "like", "%{$lab_type}%")->get("lab_name");
        if ($labs->isEmpty()) {
            return $this->returnErrorMessage("لا توجد مخابر من النوع $lab_type ",  200);
        }
        return $this->returnData("labs", $labs, "المخابر من النوع $lab_type", 200);
    }
    public function show_lab_cases_as_doctor($lab_id)
    {
        $dentist_id = Auth::id();
        $lab_cases_as_doctor = MedicalCase::where("dentist_id", $dentist_id)
            ->where("lab_manager_id", $lab_id)
            ->orderBy('id', 'desc')
            // ->orderBy('expected_delivery_date', 'desc')
            ->with(['patient:id,full_name'])
            // ->get(["created_at", 'patient_id', 'expected_delivery_date']);
            ->get(['id', 'patient_id', 'expected_delivery_date', "created_at"]);
        // ->load("patient");

        return $lab_cases_as_doctor;
    }
    public function confirm_delivery($medical_case_id)
    {
        $medical_case = MedicalCase::where("id", $medical_case_id)->first();
        $medical_case->status = 5;
        $medical_case->confirm_delivery = true;
        $medical_case->save();
        // $user->update(["confirm_delivery" => true]);
        // $medical_case->save();
        return true;
    }

    ///////////////////////////////////////
    public function getAll()
    {
        return MedicalCase::all();
    }

    public function getById($id)
    {
        $medicalCase = MedicalCase::findOrFail($id);
        $medicalCaseFiles = File::where("medical_case_id", $medicalCase->id)->get();
        $patient_details = Patient::where("id", $medicalCase->patient_id)->get();

        return [
            "medical_case_details" => $medicalCase,
            "patient_details" => $patient_details,
            "medical_case_files" => $medicalCaseFiles,
        ];
    }

    public function getPaginate($perPage = 10)
    {
        return MedicalCase::paginate($perPage);
    }

    public function create($data)
    {
        $dentist_id = Auth::id();
        $patient = Patient::where("id", $data['patient_id'])->first();

        $patient_birthday = Carbon::parse($patient->birthday); // First date
        $patient_date_at_case = Carbon::now()->format('Y-m-d')/*->toDateString()*/; // Second date
        $patient_case_age_accurate = $patient_birthday->diffInYears($patient_date_at_case); //result in years with digit point
        $patient_age_years = explode('.', $patient_case_age_accurate)[0]; //result just in years

        $medicalCase = MedicalCase::create([
            'dentist_id' => $dentist_id,
            'lab_manager_id' => $data['lab_manager_id'],
            'patient_id' => $data['patient_id'],

            'age' => $patient_age_years,

            'need_trial' => $data['need_trial'],
            'repeat' => $data['repeat'],
            'shade' => $data['shade'],
            'expected_delivery_date' => $data['expected_delivery_date'],
            'notes' => $data['notes'],
            'status' => 1, //ordered
            'confirm_delivery' => 0,
            'cost' => 0,

            'teeth_crown' => $data['teeth_crown'],
            'teeth_pontic' => $data['teeth_pontic'],
            'teeth_implant' => $data['teeth_implant'],
            'teeth_veneer' => $data['teeth_veneer'],
            'teeth_inlay' => $data['teeth_inlay'],
            'teeth_denture' => $data['teeth_denture'],

            'bridges_crown' => $data['bridges_crown'],
            'bridges_pontic' => $data['bridges_pontic'],
            'bridges_implant' => $data['bridges_implant'],
            'bridges_veneer' => $data['bridges_veneer'],
            'bridges_inlay' => $data['bridges_inlay'],
            'bridges_denture' => $data['bridges_denture'],

        ]);
        $treatment = Treatment::findOrFail($data['treatment_id']);
        $treatment->update([
            'medical_case_id' => $medicalCase->id,
        ]);
        $treatment->save();
        return $medicalCase;
    }

    public function update($case_id, $request)
    {
        $medicalCase = MedicalCase::findOrFail($case_id);
        $medicalCase->update([
            'lab_manager_id' => $request['lab_manager_id'],
            'patient_id' => $request['patient_id'],

            'need_trial' => $request['need_trial'],
            'repeat' => $request['repeat'],
            'shade' => $request['shade'],
            'expected_delivery_date' => $request['expected_delivery_date'],
            'notes' => $request['notes'],

            'teeth_crown' => $request['teeth_crown'],
            'teeth_pontic' => $request['teeth_pontic'],
            'teeth_implant' => $request['teeth_implant'],
            'teeth_veneer' => $request['teeth_veneer'],
            'teeth_inlay' => $request['teeth_inlay'],
            'teeth_denture' => $request['teeth_denture'],

            'bridges_crown' => $request['bridges_crown'],
            'bridges_pontic' => $request['bridges_pontic'],
            'bridges_implant' => $request['bridges_implant'],
            'bridges_veneer' => $request['bridges_veneer'],
            'bridges_inlay' => $request['bridges_inlay'],
            'bridges_denture' => $request['bridges_denture'],

        ]);
        $medicalCase->save();
        $treatment = Treatment::findOrFail($request['treatment_id']);
        $treatment->update([
            'medical_case_id' => $medicalCase->id,
        ]);
        $treatment->save();
        return $medicalCase;
    }

    public function delete($id)
    {
        return MedicalCase::destroy($id);
    }
}
