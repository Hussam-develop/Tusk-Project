<?php

namespace App\Http\Controllers;

use App\Http\Requests\PatientPaymentRequest;
use App\Services\PatientPaymentService;
use Illuminate\Http\Request;

class PatientPaymentController extends Controller
{
    public function __construct(protected PatientPaymentService $patientPaymentService)
    {
        $this->patientPaymentService = $patientPaymentService;
    }
    public function get_patients_payments_ordered()
    {
        $data = $this->patientPaymentService->get_patients_payments_ordered();
        return $data;
    }
    public function get_patient_payments($patient_id)
    {
        $data = $this->patientPaymentService->get_patient_payments($patient_id);
        return $data;
    }
    public function add_patient_payment(PatientPaymentRequest $request)
    {
        $data = $this->patientPaymentService->add_patient_payments($request);
        return $data;
    }
}
