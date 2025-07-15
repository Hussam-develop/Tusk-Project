<?php

namespace App\Http\Controllers;

use App\Http\Requests\BillRequest;
use App\Services\BillService;
use Illuminate\Http\Request;

class BillController extends Controller
{
    public function __construct(protected BillService $billService)
    {
        $this->billService = $billService;
    }
    public function show_lab_bills_descending($lab_id)
    {
        $data = $this->billService->show_lab_bills_descending_as_dentist($lab_id);
        return $data;
    }
    public function show_bill_details_with_cases_as_dentist($bill_id)
    {
        $data = $this->billService->show_bill_details_with_cases_as_dentist($bill_id);
        return $data;
    }
    public function addBill(BillRequest $request)
    {
        $data = $this->billService->addBill($request);
        return $data;
    }
    public function preview_bill(BillRequest $request)
    {
        $data = $this->billService->preview_bill($request);
        return $data;
    }
    public function show_lab_bills()
    {
        $data = $this->billService->show_lab_bills();
        return $data;
    }
    public function show_dentist_bills($dentist_id)
    {
        $data = $this->billService->show_dentist_bills($dentist_id);
        return $data;
    }
    public function show_bill_details($bill_id)
    {
        $data = $this->billService->show_bill_details($bill_id);
        return $data;
    }
    public function search_filter_bills(Request $request)
    {
        $data = $this->billService->search_filter_bills($request);
        return $data;
    }
}
