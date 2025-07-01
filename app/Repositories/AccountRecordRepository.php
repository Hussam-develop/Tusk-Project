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
    public function Most_profitable_doctors($user_id)
    {

        // حساب بداية ونهاية الشهر السابق
        $startOfLastMonth = \Carbon\Carbon::now()->startOfMonth()->subMonth();
        $endOfLastMonth = \Carbon\Carbon::now()->startOfMonth()->subSecond();

        // استعلام يجلب البيانات ويجمع حسب dentist_id مع الاسم الكامل
        $results = \App\Models\AccountRecord::where('lab_manager_id', $user_id)
            ->where('type', 'سحب من الرصيد (فاتورة)')
            ->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
            ->whereHas('dentist') // تأكد من وجود علاقة في النموذج
            ->with('dentist') // لتحميل البيانات
            ->get()
            ->groupBy('dentist_id')
            ->map(function ($records, $dentistId) {
                $dentist = $records->first()->dentist;
                return [
                    'fullname' => $dentist->last_name . ' ' . $dentist->first_name,
                    'dentist_id' => $dentist->id,
                    'total_signed_value' => $records->sum('signed_value'),
                ];
            })->values();

        return $results;
    }
}
