<?php

namespace App\Services;

use app\Traits\handleResponseTrait;
use App\Repositories\BillRepository;

class BillService
{
    use handleResponseTrait;

    protected $repository;

    public function __construct(BillRepository $repository)
    {
        $this->repository = $repository;
    }

    public function show_lab_bills_descending_as_dentist($lab_id)
    {
        $lab_bills = $this->repository->get_lab_bills_descending_as_dentist($lab_id);
        if ($lab_bills["lab_bills"]->isNotEmpty()) {
            return $this->returnData("lab_bills", $lab_bills, "الفواتير", 200);
        }
        return $this->returnErrorMessage("لا توجد فواتير بعد لهذا المخبر",  200);
    }
    public function show_bill_details_with_cases_as_dentist($bill_id)
    {
        $bill_details = $this->repository->show_bill_details_with_cases_as_dentist($bill_id);
        if ($bill_details['bill']) {
            return $this->returnData("bill_details", $bill_details, "تفاصيل الفاتورة", 200);
        }
        return $this->returnErrorMessage("حدث خطأ ما . لايمكن عرض تفاصيل الفاتورة",  200);
    }

    /////////////////////////////////////////////////
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
