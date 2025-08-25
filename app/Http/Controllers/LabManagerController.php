<?php

namespace App\Http\Controllers;

use App\Services\AccountRecordService;
use App\Services\CategoryService;
use App\Services\ItemhistoryService;
use App\Services\ItemService;
use App\Services\MedicalCaseService;
use App\Services\OperatingPaymentService;
use Illuminate\Http\Request;


class LabManagerController extends Controller
{
    protected $categoryService;
    protected $AccountRecordService;
    protected $ItemService;
    protected $ItemhistoryService;
    protected $OperatingPaymentService;
    protected $medicalCaseService;


    public function __construct(CategoryService $categoryService, AccountRecordService $AccountRecordService, ItemService $ItemService, ItemhistoryService $ItemhistoryService, OperatingPaymentService $OperatingPaymentService, MedicalCaseService $medicalCaseService)
    {
        $this->categoryService = $categoryService;
        $this->AccountRecordService = $AccountRecordService;
        $this->ItemService = $ItemService;
        $this->ItemhistoryService = $ItemhistoryService;
        $this->OperatingPaymentService = $OperatingPaymentService;
        $this->medicalCaseService = $medicalCaseService;
    }
    public function categories_statistics()
    {
        return $this->categoryService->categories_statistics();
    }
    public function Most_profitable_doctors()
    {
        return $this->AccountRecordService->Most_profitable_doctors();
    }
    public function items_of_user()
    {
        return $this->ItemService->items_of_user();
    }
    public function The_monthly_consumption_of_item($itemid)
    {
        return $this->ItemhistoryService->The_monthly_consumption_of_item($itemid);
    }
    public function monthly_number_of_manufactured_pieces()
    {
        $data = $this->medicalCaseService->monthly_number_of_manufactured_pieces();
        return $data;
    }
    public function Operating_Payment_statistics()
    {
        return $this->OperatingPaymentService->Operating_Payment_statistics1();
    }
    public function showLabItemsHistories()
    {
        return $this->ItemService->showLabItemsHistories();
    }
}
