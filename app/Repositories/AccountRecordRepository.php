<?php

namespace App\Repositories;

use App\Models\Dentist;
use App\Models\Accountant;

use Illuminate\Http\Request;
use App\Models\AccountRecord;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\AddPaymentRequest;



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
    public function show_dentist_payments_in_lab($labManagerid, $dentist_id)
    {

        $dentist_payments  = AccountRecord::where('lab_manager_id', $labManagerid)
            ->where('dentist_id', $dentist_id)
            ->where('type', 'إضافة رصيد')
            ->orderBy('created_at', 'desc')
            ->get(["id", "signed_value", "created_at"]);


        return $dentist_payments;
    }

    public function add_dentist_payments_in_lab($dentist_id, AddPaymentRequest $request)
    {
        $user = auth()->user(); // المستخدم الحالي بعد تحديد Guard بواسطة Middleware
        $type = $user->getMorphClass();
        $labManagerId = null;
        //  dd($type); // نوع المستخدم، مثلاً App\Models\Admin
        if ($type == "labManager") {
            $labManagerId = $user->id;
        }
        if ($type == "accountant") {
            $employeeId = $user->id;
            $labManagerId = Accountant::where('id', $employeeId)->value('lab_manager_id');
        }

        $record = AccountRecord::where('dentist_id', $dentist_id)
            ->where('lab_manager_id', $labManagerId)
            ->orderBy('created_at', 'desc')
            ->first();

        $previous_client_account_value = optional($record)->current_account ?? 0;


        $add_dentist_payments  = AccountRecord::create([
            'dentist_id' => $dentist_id,
            'lab_manager_id' => $labManagerId,
            'bill_id' => null,

            'creatorable_id' => $user->id,
            'creatorable_type' => $type,
            'note' => "-",
            'type' => "إضافة رصيد",
            'signed_value' => $request->value,
            'current_account' => $previous_client_account_value + $request->value,

        ]);

        return $add_dentist_payments;
    }
    public function createAccountRecord($data)
    {
        // 3. إنشاء سجل محاسبي للطبيب
        AccountRecord::create($data);
        /*
        'dentist_id' => $dentistId,
        'lab_manager_id' => $lab_id,
        'bill_id' => null, // لا توجد فاتورة حالياً
        'type' => $type, // أو 'new_dentist'
        'signed_value' => 0,
        'current_account' => 0,
        'creatorable_id' => $lab_id,
        'creatorable_type' => get_class($this->user),
        'note' => 'سجل مبدئي للطبيب الجديد من قبل مدير المخبر',
         */
    }
}
