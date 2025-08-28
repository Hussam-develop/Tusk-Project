<?php

namespace App\Http\Controllers;

use App\Http\Requests\TreatmentRequest;
use App\Models\Patient;
use App\Services\TreatmentService;
use app\Traits\HandleResponseTrait;
use Illuminate\Http\Request;

class TreatmentController extends Controller
{
    use HandleResponseTrait;

    public function __construct(protected TreatmentService $treatmentService)
    {
        $this->treatmentService = $treatmentService;
    }

    public function show_patient_treatments($patient_id)
    {

        $patient_treatments = $this->treatmentService->show_patient_treatments($patient_id);

        return $this->returnData("patient_treatments", $patient_treatments, "الجلسات العلاجية للمريض", 200);
    }
    public function show_treatment_details($treatment_id)
    {
        $treatment = $this->treatmentService->show_treatment_details($treatment_id);

        return $this->returnData("treatment_details", $treatment, "تفاصيل الجلسة", 200);
    }
    public function add_treatment(TreatmentRequest $request)
    {
        $data = $this->treatmentService->add_treatment($request);
        return $data;
    }
    public function update_treatment($treatment_id, TreatmentRequest $request)
    {
        $data = $this->treatmentService->update_treatment($treatment_id, $request);
        return $data;
    }

    public function download_treatment_image($file_id)
    {
        $data = $this->treatmentService->download_treatment_image($file_id);

        return $data;
    }
    public function add_treatment_image($treatment_id, Request $request)
    {
        $data = $this->treatmentService->add_treatment_image($treatment_id, $request);

        return $data;
    }
}
