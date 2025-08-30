<?php

namespace App\Services;

use app\Traits\handleResponseTrait;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AddPaymentRequest;
use App\Http\Controllers\Auth\MailController;
use App\Repositories\AccountRecordRepository;



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
    public function show_dentist_payments_in_lab($dentist_id)
    {
        $labManager = auth()->user(); // المستخدم الحالي بعد تحديد Guard بواسطة Middleware
        $result = $this->AccountRecordRepository->show_dentist_payments_in_lab($labManager->id, $dentist_id);
        if ($result->isEmpty()) {
            return $this->returnErrorMessage('ليس لدى الطبيب دفعات بعد ', 200);
        }
        return $this->returnData("dentist_payments", $result, "دفعات الطبيب", 200);

        // return $result;
    }
    public function add_dentist_payments_in_lab($dentist_id, AddPaymentRequest $request)
    {
        $result = $this->AccountRecordRepository->add_dentist_payments_in_lab($dentist_id,  $request);
        if ($result) {
            return $this->returnSuccessMessage(200, "تم إضافة دفعة جديدة للطبيب");
        }
        return $this->returnErrorMessage('حدث خطأ أثناء دفع المبلغ للطبيب', 200);

        // return $result;
    }
    public function createAccountRecourd($data)
    {
        $this->AccountRecordRepository->createAccountRecord($data);
    }
}
//
