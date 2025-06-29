<?php

namespace App\Http\Controllers;

use App\Http\Requests\MedicalCaseRequest;
use App\Services\MedicalCaseService;
use Illuminate\Http\Request;

class MedicalCaseController extends Controller
{
    public function __construct(protected MedicalCaseService $medicalCaseService)
    {
        $this->medicalCaseService = $medicalCaseService;
    }
    public function get_labs_by_labtype($lab_type)
    {
        $data = $this->medicalCaseService->get_labs_by_labtype($lab_type);
        return $data;
    }
    public function show_lab_cases_as_doctor($lab_id)
    {
        $data = $this->medicalCaseService->show_lab_cases_as_doctor($lab_id);
        return $data;
    }
    public function get_medical_case_details($medical_case_id)
    {
        $data = $this->medicalCaseService->get_medical_case_details($medical_case_id);
        return $data;
    }
    public function add_medical_case_to_lab(MedicalCaseRequest $request)
    {
        $data = $this->medicalCaseService->add_medical_case_to_lab($request);
        return $data;
    }
    public function update_case($case_id, MedicalCaseRequest $request)
    {
        $data = $this->medicalCaseService->update_case($case_id, $request);
        return $data;
    }
    public function delete_case($case_id)
    {
        $data = $this->medicalCaseService->delete($case_id);
        return $data;
    }
    public function delete_request($medical_case_id)
    {
        $data = $this->medicalCaseService->delete_request($medical_case_id);
        return $data;
    }
    public function confirm_delivery($medical_case_id)
    {
        $data = $this->medicalCaseService->confirm_delivery($medical_case_id);
        return $data;
    }
    public function add_case_images($case_id, Request $request)
    {
        $data = $this->medicalCaseService->add_case_images($case_id, $request);
        return $data;
    }
    public function download_medical_case_image($case_id)
    {
        $data = $this->medicalCaseService->download_medical_case_image($case_id);
        return $data;
    }

    ///////////////////////////////////////////////////////////////////////////////
    ////////////////////   Lab Manager Methods    /////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////

    public function show_lab_cases_by_type()
    {
        $data = $this->medicalCaseService->show_lab_cases_by_type();
        return $data;
    }
}
