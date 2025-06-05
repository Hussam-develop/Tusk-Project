<?php

namespace App\Repositories;

use App\Models\Dentist;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class PatientRepository
{
    public function paitents_statistic($user_id, $type)
    {
        if ($type == "dentist") {
            $dentist = Dentist::where('id', $user_id)->first();
            $patients = $dentist->patients;
        } else {
            return false;
        }
        if ($patients->isEmpty()) {
            return 'لا مرضى';
        }
        if ($type == "dentist") {
            // استخرج عدد المرضى لكل شهر للطبيب بمعرف 1 واحتفظ بالنتائج كمجموعة
            $resultsCollection = collect();

            $stats = Patient::selectRaw('MONTH(created_at) as month, COUNT(*) as total_patients')
                ->where('dentist_id', $user_id)
                ->groupBy('month')
                ->get();

            foreach ($stats as $stat) {
                $resultsCollection->push([
                    'month' => $stat->month,
                    'patient_count' => $stat->total_patients,
                ]);
            }

            // الآن النتائج مخزنة في مجموعة $resultsCollection
            return $resultsCollection;
        } else return false;
    }
    public function getAllWithPaginate($perPage)
    {
        return Patient::paginate($perPage);
    }

    public function getAllWithFilter(Request $request)
    {
        $query = Patient::query();

        if ($request->has('full_name')) {
            $query->where('full_name', 'LIKE', '%' . $request->full_name . '%');
        }
        if ($request->has('sort_by')) {
            switch ($request->sort_by) {
                case 'full_name_asc':
                    $query->orderBy('full_name', 'asc');
                    break;

                case 'full_name_desc':
                    $query->orderBy('full_name', 'desc');
                    break;

                case 'current_balance_asc':
                    $query->orderBy('current_balance', 'asc');
                    break;

                case 'current_balance_desc':
                    $query->orderBy('current_balance', 'desc');
                    break;

                case 'created_at_asc':
                    $query->orderBy('created_at', 'asc');
                    break;

                case 'created_at_desc':
                    $query->orderBy('created_at', 'desc');
                    break;

                case 'treatment_desc':
                    $query->whereHas('treatments', function ($q) {
                        $q->orderBy('created_at', 'desc');
                    });
                    /* $query->join('treatments', 'patients.id', '=', 'treatments.patient_id')
                        ->orderBy('treatments.created_at', 'desc')
                        ->select('patients.id', 'patients.phone', 'patients.full_name')
                        ->distinct(); */
                    break;
                case 'treatment_asc':
                    $query->whereHas('treatments', function ($q) {
                        $q->orderBy('created_at', 'asc');
                    });
                    /* $query->join('treatments', 'patients.id', '=', 'treatments.patient_id')
                        ->orderBy('treatments.created_at', 'desc')
                        ->select('patients.id', 'patients.phone', 'patients.full_name')
                        ->distinct(); */
                    break;

                    // $direction = $request->sort_by === 'appointment_asc' ? 'asc' : 'desc';

            }
        } else {
            $query->orderBy('full_name', 'asc');
        }

        return $query->paginate(10);
    }


    public function find($patientId)
    {
        return Patient::findOrFail($patientId);
    }

    public function store(array $data)
    {
        $data['dentist_id'] = Auth::id();
        $data['current_balance'] = 0;
        return Patient::create($data);
    }

    public function update($patientId, array $data)
    {
        $patient = $this->find($patientId);
        $patient->update($data);
    }

    public function show($patientId)
    {
        return $patient = $this->find($patientId)->load("images");
    }

    public function delete($patientId)
    {
        $patient = $this->find($patientId);
        $patient->delete();
    }
}
