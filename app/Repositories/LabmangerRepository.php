<?php

namespace App\Repositories;

use App\Models\Dentist;
use App\Models\Accountant;
use App\Models\LabManager;
use App\Models\AccountRecord;
use App\Models\DentistLabManager;
use App\Models\InventoryEmployee;



class LabmangerRepository
{
    public function get_labs_dentist_joined($id, $type)
    {
        if ($type == 'dentist') {
            $dentist = Dentist::find($id);

            $labs = $dentist->labManager()->where('request_is_accepted', 1)->select('lab_name', 'lab_logo')->get();
            return $labs;
        }
        return false;
    }
    public function show_account_of_dentist_in_lab($lab_id, $id, $type)
    {
        if ($type == 'dentist') {
            $latestAccountRecord = AccountRecord::where('dentist_id', $id)
                ->where('lab_manager_id', $lab_id)
                ->orderBy('created_at', 'desc')  // أو 'updated_at' حسب الحقل المستخدم
                ->first('current_account');  // يأخذ آخر سجل بناءً على الترتيب
            // dd($latestAccountRecord);
            return $latestAccountRecord;
        }
        return 'ليس طبيب'; // أو القيمة ذاتها، حسب المطلوب;

    }
    public function show_all_labs($id, $type)
    {

        if ($type == 'dentist') {
            // جلب المختبرات التي ليست مشتركة مع الطبيب
            $labs = LabManager::whereNotIn('id', function ($query) use ($id) {
                $query->select('lab_manager_id')
                    ->from('dentist_labmanagers')
                    ->where('dentist_id', $id)
                    ->where('request_is_accepted', 1);
            })
                ->paginate(10, ['id', 'lab_name', 'lab_logo']);

            return $labs;
        }
        return 'ليس طبيب';
    }
    public function show_lab_not_injoied_details($lab_id, $id, $type)
    {

        if ($type == 'dentist') {



            $lab = LabManager::find($lab_id);

            if ($lab) {
                // دمج الاسم الأول والاسم الأخير
                $full_name = $lab->first_name . ' ' . $lab->last_name;

                // عرض التفاصيل مع الاسم المدمج
                return [
                    'full_name' => $full_name,
                    'work_from_hour' => $lab->work_from_hour,
                    'work_to_hour' => $lab->work_to_hour,
                    'lab_phone' => $lab->lab_phone,
                    'lab_name' => $lab->lab_name,
                    'lab_address' => $lab->lab_address,
                    'lab_logo' => $lab->lab_logo,
                    'lab_type' => $lab->lab_type,
                ];
            } else {
                return 'المخبر غير موجود.';
            }
        } else {
            return 'ليس طبيب';
        }
    }
    public function submit_join_request_to_lab($id, $user_id, $type)
    {
        $lab = LabManager::find($id);
        if ($lab) {
            if ($type == 'dentist') {
                $existingRequest = DentistLabManager::where('lab_manager_id', $id)
                    ->where('dentist_id', $user_id)
                    ->first();

                if ($existingRequest) {
                    return 'لقد أرسلت طلبًا سابقًا.';
                }
                DentistLabManager::create([
                    'lab_manager_id' => $id,
                    'dentist_id' => $user_id,
                    'request_is_accepted' => null,
                ]);
                return 'تم إرسال الطلب بنجاح.';
            } else {
                return 'ليس طبيب';
            }
        } else {
            return 'المخبر غير موجود.';
        }
    }
    public function filter_not_join_labs($id, $type, $province = null, $name = null)
    {
        if ($type == 'dentist') {
            $query = LabManager::whereNotIn('id', function ($query) use ($id) {
                $query->select('lab_manager_id')
                    ->from('dentist_labmanagers')
                    ->where('dentist_id', $id)
                    ->where('request_is_accepted', 1);
            });

            // فلاتر باستخدام SOUNDEX على lab_province
            if ($province) {
                $query->whereRaw('SOUNDEX(lab_province) = SOUNDEX(?)', [$province]);
            }

            // فلاتر باستخدام SOUNDEX على lab_name
            if ($name) {
                $query->whereRaw('SOUNDEX(lab_name) = SOUNDEX(?)', [$name]);
            }

            $labs = $query->paginate(10, ['id', 'lab_name', 'lab_logo']);

            return $labs;
        }
        return 'ليس طبيب';
    }


    public function find($id)
    {
        return LabManager::findOrFail($id);
    }

    public function getJoinRequests($labManager)
    {
        return $labManager->dentist()
            ->wherePivot('request_is_accepted', null)
            ->get();
    }

    public function getClients($labManagerId)
    {
        $labManager = LabManager::find($labManagerId);
        return $labManager->dentist()
            ->with('latestAccountRecord')
            ->wherePivot('request_is_accepted', true)
            ->latest('dentist_labmanagers.created_at')
            ->paginate(10);
    }

    public function approveRequest($labManager, $dentistId)
    {
        // تحديث قيمة request_is_accepted في جدول pivot

        return $labManager->dentist()
            ->updateExistingPivot($dentistId, ['request_is_accepted' => 1]);
    }

    public function getActiveInventoryEmployee($labManagerId)
    {
        return InventoryEmployee::where('lab_manager_id', $labManagerId)
            ->where('active', 1)
            ->first();
    }

    public function getInactiveInventoryEmployees($labManagerId)
    {
        return InventoryEmployee::where('lab_manager_id', $labManagerId)
            ->where('active', 0)
            ->get();
    }

    public function getActiveAccountant($labManagerId)
    {
        return Accountant::where('lab_manager_id', $labManagerId)
            ->where('active', 1)
            ->first();
    }

    public function getInactiveAccountants($labManagerId)
    {
        return Accountant::where('lab_manager_id', $labManagerId)
            ->where('active', 0)
            ->get();
    }

    public function findAccountant($accountantId)
    {
        return Accountant::where('id', $accountantId)
            ->where('active', 1);
    }

    public function findInventoryEmployee($inventoryEmpId)
    {
        return InventoryEmployee::where('id', $inventoryEmpId)
            ->where('active', 1);
    }

    // إضافة موظف (مخزون او محاسب)
    public function addEmployee($data, $guard)
    {
        $models = [
            'inventory_employee' => InventoryEmployee::class,
            'accountant' => Accountant::class
        ];

        $modelClass = $models[$guard];
        $user = $modelClass::create($data);
    }



    // تعديل موظف مخزون
    public function updateInventoryEmployee($inventoryEmpId, $data)
    {
        $inventoryEmpQuery = $this->findInventoryEmployee($inventoryEmpId);
        $inventoryEmpQuery->update($data);
    }

    // حذف موظف مخزون
    public function InventoryEmployeeTermination($inventoryEmpId)
    {
        $inventoryEmpQuery = $this->findInventoryEmployee($inventoryEmpId);
        $inventoryEmpQuery->update([
            'active' => 0
        ]);
    }


    // تعديل موظف مخزون
    public function updateAccountant($accountantId, $data)
    {
        return Accountant::where('id', $accountantId)
            ->where('active', 1)
            ->update($data);
    }

    // حذف محاسب
    public function AccountantTermination($accountantId)
    {
        $accountantQuery = $this->findAccountant($accountantId);
        $accountantQuery->update([
            'active' => 0
        ]);
    }



    public function createDentist($data)
    {
        // $data['register_subscription_duration'] = null;
        return Dentist::create($data);
    }

    public function joinToLabManager(Dentist $dentist, int $labManagerId): void
    {
        $dentist->lab()->attach($labManagerId, [
            'request_is_accepted' => 1
        ]);
    }
}
