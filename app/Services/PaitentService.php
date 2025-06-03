<?php

namespace App\Services;

use App\Http\Controllers\Auth\MailController;

use app\Traits\handleResponseTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Repositories\PatientRepository;



class PaitentService
{
    use handleResponseTrait;
    protected $PatientRepository;

    public function __construct(PatientRepository $PatientRepository)
    {
        $this->PatientRepository = $PatientRepository;
    }
    public function paitents_statistics()
    {
        $user = auth()->user(); // المستخدم الحالي بعد تحديد Guard بواسطة Middleware
        $type = $user->getMorphClass();
        $result = $this->PatientRepository->paitents_statistic($user->id, $type);
        if (!$result) {
            return $this->returnErrorMessage('حدث خطأ  انت لست طبيب  ', 500);
        }
        if ($result === 'لا مرضى') {
            return $this->returnErrorMessage('ليس لديك مرضى', 500);
        }


        return $this->returnData("paitents_statistic", $result, " احصائيات المرضى ", 200);
    }
}
//
