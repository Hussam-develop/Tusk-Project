<?php

namespace App\Repositories;

use App\Models\Secretary;
use App\Models\Dentist;

class SecretaryRepository
{
    public function getSecretariesByDentistId($dentistId)
    {
        $secretaries = Secretary::where('dentist_id', $dentistId)->select('id', 'first_name', 'last_name', 'phone', 'email', 'attendence_time', 'address')->get();
        return $secretaries->map(function ($secretary) {
            $secretary->full_name = $secretary->first_name . ' ' . $secretary->last_name;
            unset($secretary->first_name, $secretary->last_name); // لحذف الحقول الأصلية إذا كنت لا تحتاجها
            return $secretary;
        });
    }
    public function findSecretaryById($id)
    {
        return Secretary::find($id);
    }

    public function updateSecretary(Secretary $secretary, array $data)
    {
        return $secretary->update($data);
    }
    public function deleteSecretary($id)
    {
        $secretary = Secretary::find($id);

        return $secretary;
    }
}
