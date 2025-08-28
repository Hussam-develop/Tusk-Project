<?php

namespace App\Services;

use App\Models\Patient;
use app\Traits\handleResponseTrait;
use Illuminate\Support\Facades\Auth;
use App\Repositories\TreatmentRepository;
use App\Repositories\PatientPaymentRepository;

class PatientPaymentService
{
    use handleResponseTrait;

    protected $repository;

    public function __construct(PatientPaymentRepository $repository)
    {
        $this->repository = $repository;
    }
    public function get_patients_payments_ordered()
    {
        $patients_payments_ordered = $this->repository->get_patients_payments_ordered();
        if ($patients_payments_ordered->isNotEmpty()) {
            return $this->returnData("patients_payments", $patients_payments_ordered, "سجل مدفوعات المرضى", 200);
        }
        return $this->returnErrorMessage("سجل مدفوعات المرضى فارغ",  200);
    }
    public function get_patient_payments($patient_id)
    {
        $dentist_id = $this->get_doctor_id();
        $patient_payments = $this->repository->getAll($dentist_id, $patient_id);

        if (!$patient_payments->isEmpty()) {
            return $this->returnData("patient_payments", $patient_payments, "سجل مدفوعات المريض", 200);
        }
        return $this->returnErrorMessage("سجل مدفوعات المريض فارغ",  200);
    }
    public function add_patient_payments($request)
    {
        $patient_payment =  $this->repository->create($request);
        if ($patient_payment) {
            $changeTreatmentsisPaid =  $this->changeTreatmentsisPaid($request);
            return $this->returnSuccessMessage(201, "تم إضافة مدفوعات المريض بنجاح");
        }
        return $this->returnErrorMessage("حدث خطأ ما أثناء إضافة مدفوعات المريض .حاول مجدداً ",  422);
    }

    public function get_doctor_id()
    {
        $user = Auth::user();
        $user_type = $user->getMorphClass();
        if ($user_type == "dentist") {
            $dentist_id = $user->id;
        } else {  //secretary
            $dentist_id = $user->dentist->id;
        }
        return $dentist_id;
    }
    public function changeTreatmentsisPaid($request)
    {
        $dentist_id = $this->get_doctor_id();

        // change Patient Balance :
        $patient = Patient::findOrFail($request->patient_id);
        $patient->current_balance += $request->value;
        $patient->save();

        $TreatmentRepository = new TreatmentRepository();
        $patient_treatments_non_paid = $TreatmentRepository->show_patient_treatments_non_paid($dentist_id, $request->patient_id);

        if ($patient_treatments_non_paid->isNotEmpty()) {

            foreach ($patient_treatments_non_paid as $treatment) {
                if ($patient->current_balance >= $treatment->cost) {
                    $patient->current_balance -= $treatment->cost;
                    $patient->save();
                    $treatment->is_paid = true;
                    $treatment->save();
                }
            }
        }
    }
    ////////////////////////////////////////////
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
