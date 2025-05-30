<?php

namespace App\Repositories;

use App\Models\Bill;
use App\Models\BillCase;
use App\Models\LabManager;
use Illuminate\Support\Facades\DB;

class BillRepository
{
    public function get_lab_bills_descending_as_dentist($lab_id)
    {
        $lab_bills = Bill::where("dentist_id", auth()->user()->id)
            ->where("lab_manager_id", $lab_id)
            ->orderByDesc("created_at")
            ->get(['id', 'total_cost', 'date_from', 'date_to', 'created_at']);
        $lab_name = LabManager::where("id", $lab_id)
            ->first(["id", "lab_name"]);

        return [
            'lab' => $lab_name,
            'bills' => $lab_bills
        ];
    }
    public function show_bill_details_with_cases_as_dentist($bill_id)
    {
        $bill = Bill::findOrFail($bill_id);
        // $bill_cases = $bill->billCases->with('patient:id,full_name')->get();
        $bill_cases = BillCase::where('bill_id', $bill->id)
            ->get();
        // $bill_cases = $bill->billCases
        //     ->with(['patient:id,full_name']);
        // ->get(['cost', 'expected_delivery_date', 'created_at']);

        $bill_cases_with_patient = DB::table('bill_cases')
            ->join('medical_cases', 'bill_cases.medical_case_id', '=', 'medical_cases.id')
            ->join('patients', 'medical_cases.patient_id', '=', 'patients.id')
            ->where('bill_cases.bill_id', '=', $bill_id) // Filter by bill_id
            ->select(
                'bill_cases.id',
                'bill_cases.bill_id',
                'bill_cases.medical_case_id',
                'bill_cases.case_cost',
                'bill_cases.created_at',
                'medical_cases.patient_id',
                'medical_cases.expected_delivery_date',
                // 'medical_cases.cost',
                'medical_cases.created_at',
                'patients.id as patient_id',
                'patients.full_name'
            )
            ->orderBy('medical_cases.expected_delivery_date', 'asc') // Sort ascending
            ->get();

        return [
            'bill' => $bill,
            'bill_cases' => $bill_cases_with_patient,
        ];
    }

    ///////////////////////////////////////////////////////
    public function getAll()
    {
        return Bill::all();
    }

    public function getById($id)
    {
        return Bill::findOrFail($id);
    }

    public function getPaginate($perPage = 10)
    {
        return Bill::paginate($perPage);
    }

    public function create(array $data)
    {
        return Bill::create($data);
    }

    public function update($id, array $data)
    {
        $item = Bill::findOrFail($id);
        $item->update($data);
        return $item;
    }

    public function delete($id)
    {
        return Bill::destroy($id);
    }
}
