<?php

namespace App\Repositories;

use Carbon\Carbon;

use App\Models\Dentist;
use App\Models\LabManager;
use App\Models\Subscription;
use App\Http\Controllers\Auth\MailController;

class AdminRepository
{
    public function getActiveLabs($perPage)
    {
        return LabManager::where('register_accepted', 1)
            ->where('subscription_is_valid_now', 1)
            ->where('email_is_verified', 1)
            ->paginate($perPage, ['id', 'lab_name', 'lab_phone', 'lab_address', 'register_date']);
    }
    public function getActiveClinics($perPage)
    {
        $dentists = Dentist::where('register_accepted', 1)
            ->where('subscription_is_valid_now', 1)
            ->where('email_is_verified', 1)
            ->paginate($perPage, ['id', 'first_name', 'last_name', 'phone', 'address', 'register_date']);

        return $dentists->map(function ($dentist) {
            $dentist->full_name = $dentist->first_name . ' ' . $dentist->last_name;
            unset($dentist->first_name, $dentist->last_name); // لحذف الحقول الأصلية إذا كنت لا تحتاجها
            return $dentist;
        });
    }

    public function filterLabs($labName = null, $registerDate = null, $perPage = 10)
    {
        $query = LabManager::where('register_accepted', 1)
            ->where('subscription_is_valid_now', 1)
            ->where('email_is_verified', 1);

        if ($labName) {
            $query->whereRaw('SOUNDEX(lab_name) = SOUNDEX(?)', [$labName]);
        }

        if ($registerDate) {
            $query->whereDate('register_date', $registerDate);
        }

        // استخدم paginate بدلاً من get
        return $query->paginate($perPage, ['id', 'lab_name', 'lab_phone', 'lab_address', 'register_date']);
    }


    public function filterclinics($clinic_name = null, $register_date = null, $perPage = 10)
    {
        $query = Dentist::where('register_accepted', 1)
            ->where('subscription_is_valid_now', 1)
            ->where('email_is_verified', 1);

        if ($clinic_name) {
            $query->where(function ($query) use ($clinic_name) {
                $query->whereRaw('SOUNDEX(first_name) = SOUNDEX(?)', [$clinic_name])
                    ->orWhereRaw('SOUNDEX(last_name) = SOUNDEX(?)', [$clinic_name]);
            });
        }




        if ($register_date) {
            $query->whereDate('register_date', $register_date);
        }

        $dentists = $query->paginate($perPage, ['id', 'first_name', 'last_name', 'phone', 'address', 'register_date']);
        return $dentists->map(function ($dentist) {
            $dentist->full_name = $dentist->first_name . ' ' . $dentist->last_name;
            unset($dentist->first_name, $dentist->last_name); // لحذف الحقول الأصلية إذا كنت لا تحتاجها
            return $dentist;
        });
    }

    public function getLabsWithNullSubscription($perPage)
    {
        return LabManager::where('register_accepted', 1)
            ->where('email_is_verified', 1)
            ->where('subscription_is_valid_now', 0)
            ->paginate($perPage, ['id', 'lab_name', 'lab_phone', 'lab_address', 'register_date']);
    }
    public function getClinicsWithNullSubscription($perPage)
    {
        $dentists = Dentist::where('register_accepted', 1)
            ->where('email_is_verified', 1)
            ->where('subscription_is_valid_now', 0)
            ->paginate($perPage, ['id', 'first_name', 'last_name', 'phone', 'address', 'register_date']);
        return $dentists->map(function ($dentist) {
            $dentist->full_name = $dentist->first_name . ' ' . $dentist->last_name;
            unset($dentist->first_name, $dentist->last_name); // لحذف الحقول الأصلية إذا كنت لا تحتاجها
            return $dentist;
        });
    }
    public function getLabsWithRegisterAcceptedZero($perPage)
    {
        return LabManager::where('register_accepted', NULL)
            ->paginate($perPage, ['id', 'lab_name', 'lab_phone', 'lab_address', 'register_date']);
    }

    public function getClinicsWithRegisterAcceptedZero($perPage)
    {
        $dentists = Dentist::where('register_accepted', NULL)
            ->paginate($perPage, ['id', 'first_name', 'last_name', 'phone', 'address', 'register_date']);
        return $dentists->map(function ($dentist) {
            $dentist->full_name = $dentist->first_name . ' ' . $dentist->last_name;
            unset($dentist->first_name, $dentist->last_name); // لحذف الحقول الأصلية إذا كنت لا تحتاجها
            return $dentist;
        });
    }
    public function renewSubscription($labId, $months, $subscriptionValue)
    {
        // التأكد من تحويل $months إلى عدد صحيح
        $months = intval($months);

        // حساب التواريخ
        $subscriptionFrom = Carbon::now();
        $subscriptionTo = $subscriptionFrom->copy()->addMonths($months);

        // إدخال سجل جديد في جدول subscriptions
        $subscription = new Subscription();
        $subscription->subscriptionable_type = 'labManager';
        $subscription->subscriptionable_id = $labId;
        $subscription->subscription_from = $subscriptionFrom;
        $subscription->subscription_to = $subscriptionTo;
        $subscription->subscription_is_valid = 1;
        $subscription->subscription_value = $subscriptionValue;
        $subscription->save();

        // تحديث القيمة subscription_is_valid_now في جدول lab_managers
        LabManager::where('id', $labId)->update(['subscription_is_valid_now' => 1]);
    }

    public function renewClinicSubscription($clinicId, $months, $subscriptionValue)
    {
        $months = intval($months);

        $subscriptionFrom = Carbon::now();
        $subscriptionTo = $subscriptionFrom->copy()->addMonths($months);

        // إدخال سجل جديد في جدول subscriptions
        $subscription = new Subscription();
        $subscription->subscriptionable_type = 'dentist';
        $subscription->subscriptionable_id = $clinicId;
        $subscription->subscription_from = $subscriptionFrom;
        $subscription->subscription_to = $subscriptionTo;
        $subscription->subscription_is_valid = 1;
        $subscription->subscription_value = $subscriptionValue;
        $subscription->save();

        // تحديث القيمة subscription_is_valid_now في جدول lab_managers
        Dentist::where('id', $clinicId)->update(['subscription_is_valid_now' => 1]);
    }
    public function updateRegisterAccepted($id)
    {
        $labManager = LabManager::find($id);
        if ($labManager) {
            $labManager->register_accepted = 1;
            $labManager->register_date = now();
            $labManager->save();

            //Send Welcome Mail
            $MailController = new MailController();
            $MailController->send_welcome_mail($labManager->email, "LabManager");

            return 'yes';
        }

        return NULL;
    }
    public function updateRegisterAcceptedclinic($id)
    {
        $clinic = Dentist::find($id);
        if ($clinic) {
            $clinic->register_accepted = 1;
            $clinic->register_date = now();

            $clinic->save();

            //Send Welcome Mail
            $MailController = new MailController();
            $MailController->send_welcome_mail($clinic->email, "Dentist");

            return 'yes';
        }
        return NULL;
    }
}
