<?php

namespace App\Repositories;

use App\Models\AccountRecord;

use App\Models\Dentist;
use Illuminate\Support\Facades\DB;



class AccountRecordRepository
{
    public function Account_records_of_lab($lab_id, $user_id, $type)
    {
        if ($type != "dentist") {
            return 'ليس طبيب';
        }
        $results = AccountRecord::where('lab_manager_id', $lab_id)
            ->where('dentist_id', $user_id)
            ->where('type', "إضافة رصيد")
            ->orderBy('created_at', 'desc')
            ->get();
        return $results;
    }
}
