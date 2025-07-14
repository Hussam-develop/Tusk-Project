<?php

namespace App\Http\Controllers\LabManager;

use App\Http\Controllers\Controller;
use App\Http\Requests\storeDentistRequest;
use App\Http\Resources\DentistResource;
use App\Services\AccountRecordService;
use App\Services\LabmangerService;
use app\Traits\handleResponseTrait;
use Exception;
use Illuminate\Http\Request;

class ClientsController extends Controller
{
    use handleResponseTrait;
    public function __construct(protected LabmangerService $labmanagerService)
    {
        $this->labmanagerService = $labmanagerService;
    }

    // عرض طلبات الانضمام
    public function showJoinRequestsToLab()
    {
        $requests = $this->labmanagerService->showJoinRequestsToLab();
        return $this->returnData('join requests', DentistResource::collection($requests), 'all join request', 200);
    }

    // قبول طلب
    public function approveJoinRequestToLab($dentistId)
    {
        try {
            $this->labmanagerService->approveJoinRequestToLab($dentistId);
            return $this->returnSuccessMessage(200, 'تمت الموافقة بنجاح');
        } catch (\RuntimeException $e) {
            return $this->returnErrorMessage($e->getMessage(), 500);
        }
    }



    //عرض العملاءالحاليين (الاطباء الذين انقبلت طلبات انضمامهم)  في مخبر معين
    public function showLabClients()
    {
        $clients = $this->labmanagerService->ShowClientsInLab();
        return $this->returnData('clients', DentistResource::collection($clients), 'all clients', 200);
    }


    // إضافة زبون محليا
    public function addDentistAsLocalClientForLabManager(storeDentistRequest $request)
    {
        try {
            $this->labmanagerService->addDentistAsLocalClientForLabManager($request->validated());

            return $this->returnSuccessMessage(200, 'تمت إضافة الزبون بنجاح');
        } catch (\RuntimeException $e) {
            return $this->returnErrorMessage($e->getMessage(), 500);
        }
    }
}
