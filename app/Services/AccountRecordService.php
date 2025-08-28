<?php

namespace App\Services;

use App\Http\Controllers\Auth\MailController;

use App\Repositories\AccountRecordRepository;
use app\Traits\handleResponseTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;



class AccountRecordService
{
    use handleResponseTrait;
    protected $AccountRecordRepository;

    public function __construct(AccountRecordRepository $AccountRecordRepository)
    {
        $this->AccountRecordRepository = $AccountRecordRepository;
    }
    public function Account_records_of_lab($lab_id)
    {
        $user = auth()->user(); // المستخدم الحالي بعد تحديد Guard بواسطة Middleware
        $type = $user->getMorphClass();
        $result = $this->AccountRecordRepository->Account_records_of_lab($lab_id, $user->id, $type);
        if ($result == 'ليس طبيب') {
            return $this->returnErrorMessage('حدث خطأ  انت لست طبيب  ', 500);
        }
        if ($result->isEmpty()) {
            return $this->returnErrorMessage('ليس لديك دفعات  في هذا المخبر ', 500);
        }
        return $this->returnData("AccountRecords of this lab", $result, " الدفعات عند هذا المخبر", 200);
    }
    public function Most_profitable_doctors()
    {
        $user = auth()->user(); // المستخدم الحالي بعد تحديد Guard بواسطة Middleware
        $result = $this->AccountRecordRepository->Most_profitable_doctors($user->id);
        if ($result->isEmpty()) {
            return $this->returnErrorMessage('ليس لديك دفعات ', 500);
        }
        return $this->returnData("Most_profitable_doctors", $result, "الاطباء الاكثر مردودا  ", 200);

        // return $result;
    }
    public function createAccountRecourd($data)
    {
        $this->AccountRecordRepository->createAccountRecord($data);
    }
}
//
