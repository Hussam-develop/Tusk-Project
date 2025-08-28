<?php

namespace App\Services;

use app\Traits\HandleResponseTrait;
use App\Repositories\PatientRepository;
use App\Repositories\OperatingPaymentRepository;

class OperatingPaymentService
{
    use HandleResponseTrait;

    protected $repository;
    protected $OperatingPaymentRepository;
    protected $PatientRepository;

    public function __construct(OperatingPaymentRepository $repository, PatientRepository $PatientRepository, OperatingPaymentRepository $OperatingPaymentRepository)
    {
        $this->repository = $repository;
        $this->PatientRepository = $PatientRepository;
        $this->OperatingPaymentRepository = $OperatingPaymentRepository;
    }
    public function get_operating_payments()
    {
        $operating_payments = $this->repository->getAll();

        if (!$operating_payments->isEmpty()) {
            return $this->returnData("operating_payments", $operating_payments, "سجل المصاريف التشغيلية", 200);
        }
        return $this->returnErrorMessage("سجل المصاريف التشغيلية فارغ",  422);
    }
    public function add_operating_payments($request)
    {
        $operating_payments =  $this->repository->create($request);
        if ($operating_payments) {
            return $this->returnSuccessMessage(201, "تم إضافة المصروف التشغيلي بنجاح");
        }
        return $this->returnErrorMessage("حدث خطأ ما أثناء عرض سجل المصاريف التشغيلية ",  422);
    }


    ///////////////////////////////////////////////////
    public function Operating_Payment_statistics()
    {
        $user = auth()->user(); // المستخدم الحالي بعد تحديد Guard بواسطة Middleware
        $type = $user->getMorphClass();
        $result1 = $this->OperatingPaymentRepository->allOperatingPayment($user->id, $type);
        $result = $this->OperatingPaymentRepository->Operating_Payment_statistics($user->id, $type);

        if ($result->isEmpty()) {

            return $this->returnErrorMessage('لا يوجد دفعات تشغيلية', 200);
        }

        foreach ($result as &$res) {
            $res['percentage'] = $result1 > 0 ? number_format(($res['total_value'] * 100) / $result1, 2) : 0;
        }
        return $this->returnData("Operating_Payments", $result, " احصائيات الدفعات التشغيلية ", 200);
    }
    public function doctor_gains_statistics()
    {
        $gains_statistics = $this->OperatingPaymentRepository->doctor_gains_statistics();

        if ($gains_statistics) {
            return $this->returnData("doctor_gains_statistics", $gains_statistics, "الأرباح ", 200);
        }
        return $this->returnErrorMessage('ليست لديك إخصائيات في الوقت الحالي', 200);
    }
    ///////////////////////////////////////////////////
    public function getAll()
    {
        return $this->repository->getAll();
    }

    public function getById($id)
    {
        return $this->repository->getById($id);
    }

    public function getPaginate($perPage = 10)
    {
        return $this->repository->getPaginate($perPage);
    }

    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    public function update($id, array $data)
    {
        return $this->repository->update($id, $data);
    }
    public function delete($id)
    {
        return $this->repository->delete($id);
    }
    public function Operating_Payment_statistics1()
    {
        $user = auth()->user(); // المستخدم الحالي بعد تحديد Guard بواسطة Middleware
        $type = $user->getMorphClass();
        $result1 = $this->OperatingPaymentRepository->allOperatingPayment1($user->id, $type);
        $result = $this->OperatingPaymentRepository->Operating_Payment_statistics1($user->id, $type);

        if ($result->isEmpty()) {

            return $this->returnErrorMessage('لا يوجد دفعات تشغيلية', 200);
        }

        foreach ($result as &$res) {
            $res['percentage'] = $result1 > 0 ? number_format(($res['total_value'] * 100) / $result1, 2) : 0;
        }
        return $this->returnData("Operating_Payments", $result, " احصائيات الدفعات التشغيلية ", 200);
    }
}
