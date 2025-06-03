<?php

namespace App\Repositories;

use App\Models\Dentist;
use App\Models\Patient;


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
}
