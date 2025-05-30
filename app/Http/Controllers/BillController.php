<?php

namespace App\Http\Controllers;

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
}
