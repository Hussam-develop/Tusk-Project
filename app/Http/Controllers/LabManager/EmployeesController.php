<?php

namespace App\Http\Controllers\LabManager;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddAccountantRequest;
use App\Http\Requests\AddEmployeeRequest;
//use App\Http\Requests\AddInventoryImpRequest;
use App\Http\Requests\UpdateAccountantRequest;
use App\Http\Requests\UpdateInventoryImpRequest;
use App\Http\Resources\LabEmployeesResource;
use App\Services\LabmangerService;
use app\Traits\HandleResponseTrait;
use Illuminate\Http\Request;

class EmployeesController extends Controller
{
    use HandleResponseTrait;
    public function __construct(protected LabmangerService $labmanagerService)
    {
        $this->labmanagerService = $labmanagerService;
    }

    // عرض موظفي المخبر(المحاسبين وموظفي المخزون ) المسرحين والحاليين
    public function showLabEmployees()
    {
        $data = $this->labmanagerService->getEmployeesForLab();

        return $this->returnData('employees', new LabEmployeesResource($data), 'الموظفين', 200);
    }

    // اضافة موظف مخزون
    public function addEmployee(AddEmployeeRequest $request)
    {
        $this->labmanagerService->addEmployee($request->validated(), $request->guard);

        return $this->returnSuccessMessage(200, 'تم إضافة الموظف بنجاح');
    }

    // تعديل موظف مخزون
    public function updateInventoryEmployee(UpdateInventoryImpRequest $request, $inventoryEmpId)
    {

        $dataInput = $request->validated();
        $this->labmanagerService->updateInventoryEmployee($inventoryEmpId, $dataInput);

        return $this->returnSuccessMessage(200, 'تم تعديل بيانات الموظف بنجاح');
    }

    // تسريح موظف مخزون
    public function inventoryEmployeeTermination($inventoryEmpId)
    {
        $this->labmanagerService->InventoryEmployeeTermination($inventoryEmpId);

        return $this->returnSuccessMessage(200, 'تم تسريح الموظف بنجاح');
    }


    // تعديل محاسب
    public function updateAccountant(UpdateAccountantRequest $request, $EmpId)
    {
        try {
            $this->labmanagerService->updateAccountant($EmpId, $request->validated());
            return $this->returnSuccessMessage(200, 'تم تعديل بيانات الموظف بنجاح');
        } catch (\RuntimeException $e) {
            return $this->returnErrorMessage($e->getMessage(), 500);
        }
    }

    // تسريح محاسب


    public function AccountantTermination($accountantId)
    {
        $this->labmanagerService->AccountantTermination($accountantId);

        return $this->returnSuccessMessage(200, 'تم تسريح الموظف بنجاح');
    }
}
