<?php

namespace App\Repositories;

use App\Models\Patient;
use App\Models\PatientPayment;

class PatientPaymentRepository
{
    public function get_patients_payments_ordered()
    {
        $patientsPaymentsOrdered =  Patient::where("dentist_id", auth()->user()->id)
            ->orderBy('current_balance', 'asc')
            ->get(['id', 'full_name', 'current_balance']);
        return $patientsPaymentsOrdered;
    }
    public function getAll($dentist_id, $patient_id)
    {
        $patientPayment =  PatientPayment::where("patient_id", $patient_id)
            ->where("dentist_id", $dentist_id)
            ->orderBy('payment_date', 'desc')
            ->get(["id", "value", "payment_date"]);
        return $patientPayment;
    }

    public function getById($id)
    {
        return PatientPayment::findOrFail($id);
    }

    public function getPaginate($perPage = 10)
    {
        return PatientPayment::paginate($perPage);
    }

    public function create($request)
    {

        $user = auth()->user();
        $user_type = $user->getMorphClass();
        if ($user_type == "dentist") {
            $dentist_id = $user->id;
        } else {  //secretary
            $dentist_id = $user->dentist->id;
        }
        $PatientPayment = PatientPayment::create([
            'patient_id' => $request->patient_id,
            'dentist_id' => $dentist_id,

            'creatorable_id' => $user->id,
            'creatorable_type' => $user_type,

            'payment_date' => $request->payment_date,
            'value' => $request->value,
        ]);
        return $PatientPayment;
    }

    public function update($id, array $data)
    {
        $item = PatientPayment::findOrFail($id);
        $item->update($data);
        return $item;
    }

    public function delete($id)
    {
        return PatientPayment::destroy($id);
    }
}
