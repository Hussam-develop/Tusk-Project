<?php

namespace App\Repositories;

use App\Models\AccountRecord;
use App\Models\Dentist;
use App\Models\DentistLabManager;
use App\Models\LabManager;



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
                    'lab_from_hour' => $lab->lab_from_hour,
                    'lab_to_hour' => $lab->lab_to_hour,
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
}
