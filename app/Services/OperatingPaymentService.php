<?php

namespace App\Services;

use app\Traits\handleResponseTrait;
use App\Repositories\OperatingPaymentRepository;

class OperatingPaymentService
{
    use handleResponseTrait;

    protected $repository;

    public function __construct(OperatingPaymentRepository $repository)
    {
        $this->repository = $repository;
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
}
