<?php

namespace App\Services;

use Exception;

use App\Models\LabManager;
use Illuminate\Support\Facades\DB;
use app\Traits\handleResponseTrait;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Repositories\LabmangerRepository;
use App\Http\Controllers\Auth\MailController;
use App\Http\Resources\latestAccountInLabResource;

class LabmangerService
{
    use handleResponseTrait;
    protected $labmangerrepository;
    protected $user;
    public function __construct(LabmangerRepository $labmangerrepository, protected AccountRecordService $accountRecordService)
    {
        $this->labmangerrepository = $labmangerrepository;
        $this->user = Auth::user();
    }
    public function get_labs_dentist_joined()
    {
        $user = auth()->user(); // المستخدم الحالي بعد تحديد Guard بواسطة Middleware
        $type = $user->getMorphClass();
        $labs = $this->labmangerrepository->get_labs_dentist_joined($user->id, $type);
        if (!$labs) {
            return $this->returnErrorMessage('حدث خطأ  انت لست طبيب  ', 500);
        }
        if (empty($labs)) {
            return $this->returnErrorMessage('لست مشترك عند اي مخير ', 500);
        }

        return $this->returnData("labs iam joind", $labs, " مخابري ", 200);
    }
    public function show_account_of_dentist_in_lab($lab_id)
    {
        $user = auth()->user(); // المستخدم الحالي بعد تحديد Guard بواسطة Middleware
        $type = $user->getMorphClass();
        $Account = $this->labmangerrepository->show_account_of_dentist_in_lab($lab_id, $user->id, $type);
        if (is_null($Account)) {
            return $this->returnErrorMessage('ليس لديك سجل حساب هنا', 500);
        }
        // if ($Account->isEmpty()){
        //      return $this->returnErrorMessage('ليس لديك سجلات حسابات ', 500);
        //  }
        if ($Account === 'ليس طبيب') {
            return $this->returnErrorMessage('لست طبيب انت غير مخول', 500);
        }
        return $this->returnData(" Latest Account of this lab ", $Account, " اخر سجل حساب لدي  ", 200);


        //latestAccountInLabResource::collection(
    }
    public function show_all_labs()
    {
        $user = auth()->user(); // المستخدم الحالي بعد تحديد Guard بواسطة Middleware
        $type = $user->getMorphClass();
        $Labs = $this->labmangerrepository->show_all_labs($user->id, $type);
        if ($Labs === 'ليس طبيب') {
            return $this->returnErrorMessage('لست طبيب انت غير مخول', 500);
        }
        if ($Labs->isEmpty()) {
            return $this->returnErrorMessage(' لا يوجد مخابر مسجلة بالمنصة أنت غير مشترك بها ', 500);
        }
        // if ($Account->isEmpty()){
        //      return $this->returnErrorMessage('ليس لديك سجلات حسابات ', 500);
        //  }

        return $this->returnData(" Latest Account of this lab ", $Labs, "المخابر المسجلة في المنصة", 200);
    }
    public function show_lab_not_injoied_details($id)
    {
        $user = auth()->user(); // المستخدم الحالي بعد تحديد Guard بواسطة Middleware
        $type = $user->getMorphClass();
        $Lab_details = $this->labmangerrepository->show_lab_not_injoied_details($id, $user->id, $type);
        if ($Lab_details === 'ليس طبيب') {
            return $this->returnErrorMessage('لست طبيب انت غير مخول', 500);
        }
        if ($Lab_details === 'المخبر غير موجود.') {
            return $this->returnErrorMessage('المخبر غير موجود', 500);
        }

        return $this->returnData(" Lab Details ", $Lab_details, 'تفاصيل المخبر ', 200);
    }
    // id ارسال طلب انضمام لمخبر معين عن طريق
    public function submit_join_request_to_lab($id)
    {
        $user = auth()->user(); // المستخدم الحالي بعد تحديد Guard بواسطة Middleware
        $type = $user->getMorphClass();
        $join_request = $this->labmangerrepository->submit_join_request_to_lab($id, $user->id, $type);
        if ($join_request === 'ليس طبيب') {
            return $this->returnErrorMessage('لست طبيب انت غير مخول', 500);
        }
        if ($join_request === 'لقد أرسلت طلبًا سابقًا.') {
            return $this->returnErrorMessage('لقد أرسلت طلبًا سابقًا.', 500);
        }

        if ($join_request === 'المخبر غير موجود.') {
            return $this->returnErrorMessage('المخبر غير موجود', 500);
        }
        return $this->returnSuccessMessage(200, 'تم إرسال الطلب بنجاح.');
    }
    public function filter_not_join_labs($province = null, $name = null)
    {
        $user = auth()->user(); // المستخدم الحالي بعد تحديد Guard بواسطة Middleware
        $type = $user->getMorphClass();
        $labs = $this->labmangerrepository->filter_not_join_labs($user->id, $type, $province, $name);
        if ($labs === 'ليس طبيب') {
            return $this->returnErrorMessage('لست طبيب انت غير مخول', 500);
        }
        if ($labs->isEmpty()) {
            return $this->returnErrorMessage('لا يوجد مخابر مشابهة للبحث', 500);
        }

        return $this->returnData("filterd labs ", $labs, " المخابر المشابهة للبحث  ", 200);
    }


    //عرض طلبات الانضمام الى مخبر معين
    public function ShowJoinRequestsToLab()
    {
        // $labManager = auth()->user();

        return $this->labmangerrepository->getJoinRequests($this->user);
    }

    //قبول طلب الانضمام الى مخبر معين
    public function ApproveJoinRequestToLab($dentistId)
    {
        //$labManager = auth()->user();
        $labManagerId = $this->user->id;
        try {
            DB::transaction(function () use ($labManagerId, $dentistId) {
                $this->labmangerrepository->approveRequest($this->user, $dentistId);

                $data_account_recourd = [
                    'dentist_id' => $dentistId,
                    'lab_manager_id' => $labManagerId,
                    'bill_id' => null, // لا توجد فاتورة حالياً
                    'type' => 'إنشاء حساب طبيب', // أو 'new_dentist'
                    'signed_value' => 0,
                    'current_account' => 0,
                    'creatorable_id' => $labManagerId,
                    'creatorable_type' => 'LabManager',
                    'note' => 'قبول طلب انضمام زبون من قبل مدير المخبر',
                ];
                $this->accountRecordService->createAccountRecourd($data_account_recourd);
            });
        } catch (Exception $e) {
            // تسجيل الخطأ في اللوج
            Log::error('❌ Failed to add dentist for Lab Manager', [
                'lab_manager_id' => $labManagerId,
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \RuntimeException('حدث خطأ أثناء قبول طلب انضمام الطبيب. يرجى المحاولة لاحقًا.');
        }
    }


    //عرض العملاء (الاطباء الذين انقبلت طلبات انضمامهم)  في مخبر معين
    public function ShowClientsInLab()
    {

        // $labManager = auth()->user();

        return $this->labmangerrepository->getClients($this->user->id);
    }

    public function getEmployeesForLab()
    {
        // $labManager = auth()->user();
        return [
            'active_inventory_employee' => $this->labmangerrepository->getActiveInventoryEmployee($this->user->id),
            'inactive_inventory_employees' => $this->labmangerrepository->getInactiveInventoryEmployees($this->user->id),

            'active_accountant' => $this->labmangerrepository->getActiveAccountant($this->user->id),
            'inactive_accountants' => $this->labmangerrepository->getInactiveAccountants($this->user->id),
        ];
    }

    // إضافة موظف مخزون
    public function addEmployee($data, $guard)
    {
        $data['lab_manager_id'] = $this->user->id;
        return $this->labmangerrepository->addEmployee($data, $guard);
    }

    // تعديل موظف مخزون
    public function updateInventoryEmployee($inventoryEmpId, $data)
    {
        $this->labmangerrepository->updateInventoryEmployee($inventoryEmpId, $data);
    }

    // حذف موظف مخزون
    public function InventoryEmployeeTermination($inventoryEmpId)
    {
        $this->labmangerrepository->InventoryEmployeeTermination($inventoryEmpId);
    }

    // تعديل موظف مخزون
    public function updateAccountant($accountantId, $data)
    {
        //  $this->labmangerrepository->updateAccountant($accountantId, $data);

        try {
            $accountant = $this->labmangerrepository->findAccountant($accountantId)->first();

            if (!$accountant) {
                throw new \RuntimeException("لا يوجد محاسب نشط بهذا المعرف: $accountantId");
            }

            $this->labmangerrepository->updateAccountant($accountant->id, $data);
        } catch (Exception $e) {
            Log::error('❌ فشل تعديل بيانات المحاسب', [
                'accountant_id' => $accountantId,
                'data' => $data,
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw new \RuntimeException('حدث خطأ أثناء تعديل بيانات المحاسب. الرجاء المحاولة لاحقًا.');
        }
    }

    // حذف محاسب
    public function AccountantTermination($accountantId)
    {

        $this->labmangerrepository->AccountantTermination($accountantId);
    }



    public function addDentistAsLocalClientForLabManager(array $data)
    {

        $labManagerId = $this->user->id;
        try {
            $transaction =
                DB::transaction(function () use ($data, $labManagerId) {
                    $dentist = $this->labmangerrepository->createDentist($data);
                    $this->labmangerrepository->joinToLabManager($dentist, $labManagerId);
                    $data_account_recourd = [
                        'dentist_id' => $dentist->id,
                        'lab_manager_id' => $labManagerId,
                        'bill_id' => null, // لا توجد فاتورة حالياً
                        'type' => 'اضافة زبون محليا', // أو 'new_dentist'
                        'signed_value' => 0,
                        'current_account' => 0,
                        'creatorable_id' => $labManagerId,
                        'creatorable_type' => 'LabManager',
                        'note' => 'سجل مبدئي للطبيب الجديد من قبل مدير المخبر',
                    ];
                    $this->accountRecordService->createAccountRecourd($data_account_recourd);
                });
        } catch (Exception $e) {
            // dd($e->getMessage());
            // تسجيل الخطأ في اللوج
            Log::error('❌ Failed to add dentist for Lab Manager', [
                'lab_manager_id' => $labManagerId,
                'data' => $data,
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \RuntimeException('حدث خطأ أثناء إضافة الطبيب. يرجى المحاولة لاحقًا.');
        }
    }
}
