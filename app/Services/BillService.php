<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Http\Requests\BillRequest;
use app\Traits\HandleResponseTrait;
use App\Repositories\BillRepository;

class BillService
{
    use HandleResponseTrait;

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
    public function addBill(BillRequest $request)
    {
        $addBill = $this->repository->addBill($request);
        if ($addBill == "done") {
            return $this->returnSuccessMessage(201, "تم إنشاء الفاتورة بنجاح");
        }
        return $this->returnErrorMessage($addBill,  200);
    }
    public function preview_bill(BillRequest $request)
    {
        $preview = $this->repository->preview_bill($request);
        if ($preview["message_status"] == "done") {
            return $this->returnData("preview", $preview["data"],  $preview["message"], 200);
        }
        return $this->returnErrorMessage($preview["message"],  200);
    }
    public function show_lab_bills()
    {
        $lab_bills = $this->repository->show_lab_bills();
        if ($lab_bills["message_status"] == "done") {
            return $this->returnData("lab_bills", $lab_bills["data"],  $lab_bills["message"], 200);
        }
        return $this->returnErrorMessage($lab_bills["message"],  200);
    }
    public function show_dentist_bills($dentist_id)
    {
        $dentist_bills = $this->repository->show_dentist_bills($dentist_id);
        // dd($dentist_bills["message"]);
        if ($dentist_bills["message_status"] == "done") {
            return $this->returnData("dentist_bills", $dentist_bills["data"],  $dentist_bills["message"], 200);
        }
        return $this->returnErrorMessage($dentist_bills["message"],  200);
    }
    public function show_bill_details($bill_id)
    {
        $bill = $this->repository->show_bill_details($bill_id);
        // dd($bill["message"]);
        if ($bill["message_status"] == "done") {
            return $this->returnData("bill", $bill["data"],  $bill["message"], 200);
        }
        return $this->returnErrorMessage($bill["message"],  200);
    }
    public function search_filter_bills(Request $request)
    {
        $bill = $this->repository->search_filter_bills($request);
        // dd($bill["message"]);
        if ($bill["message_status"] == "done") {
            return $this->returnData("bill", $bill["data"],  $bill["message"], 200);
        }
        return $this->returnErrorMessage($bill["message"],  200);
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
