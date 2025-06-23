<?php

namespace App\Repositories;

use Carbon\Carbon;

use App\Models\Dentist;
use App\Models\LabManager;
use App\Models\Subscription;
use App\Http\Controllers\Auth\MailController;

class AdminRepository
{
    public function getActiveLabs(/*$perPage*/)
    {
        /* old wrong code
        return LabManager::subscriptionIsValidNow()
            ->paginate($perPage, ['id', 'lab_name', 'lab_phone', 'lab_address', 'register_date']);
      */

        //  Without Pagination :
        $subscriptionable_type = "labManager";
        $this->check_non_subscribers($subscriptionable_type);

        $labs_subscriptions = Subscription::where('subscriptionable_type', 'labManager')
            ->where('subscription_is_valid', true)
            ->whereDate('subscription_from', '<=', Carbon::now())
            ->whereDate('subscription_to', '>=', Carbon::now())
            ->whereHas('subscriptionable', function ($query) {
                $query->where('subscription_is_valid_now', true);
            })
            ->with(['subscriptionable' => function ($query) {
                $query->select('id', "lab_name", 'lab_phone', 'lab_address');
            }])->get(
                [
                    'subscriptionable_id', // LabManager or Dentist
                    'subscriptionable_type',
                    'subscription_from',
                    'subscription_to',
                ]
            )
            ->map(function ($subscription) {
                $from = Carbon::parse($subscription->subscription_from);
                $to = Carbon::parse($subscription->subscription_to);
                $subscription->duration_as_months = $from->floatDiffInMonths($to);
                return $subscription;
            });

        // dd($labs_subscriptions);


        // With Pagination :
        // $labs_subscriptions = Subscription::where('subscriptionable_type', 'labManager')
        //     // ->where('subscription_is_valid', true)
        //     ->whereDate('subscription_from', '<=', Carbon::now())
        //     ->whereDate('subscription_to', '>=', Carbon::now())
        //     ->orderBy("subscription_to")
        //     ->with(['subscriptionable' => function ($query) {
        //         $query->select('id', "lab_name", 'lab_phone', 'lab_address');
        //     }])->paginate($perPage, [
        //         'subscriptionable_id', // LabManager or Dentist
        //         'subscriptionable_type',
        //         'subscription_from',
        //         'subscription_to',
        //     ]);

        // $labs_subscriptions->getCollection()->transform(function ($subscription) {
        //     $from = Carbon::parse($subscription->subscription_from);
        //     $to = Carbon::parse($subscription->subscription_to);
        //     $subscription->duration_as_months = $from->floatDiffInMonths($to);
        //     return $subscription;
        // });

        return $labs_subscriptions;
    }
    public function getActiveClinics(/*$perPage*/)
    {
        //old code :

        // $dentists = Dentist::subscriptionIsValidNow()
        //     ->paginate($perPage, ['id', 'first_name', 'last_name', 'phone', 'address', 'register_date']);

        // return $dentists->map(function ($dentist) {
        //     $dentist->full_name = $dentist->first_name . ' ' . $dentist->last_name;
        //     unset($dentist->first_name, $dentist->last_name); // لحذف الحقول الأصلية إذا كنت لا تحتاجها
        //     return $dentist;
        // });

        // end old code

        //  Without Pagination :
        $subscriptionable_type = "dentist";
        $this->check_non_subscribers($subscriptionable_type);

        $clinics_subscriptions = Subscription::where('subscriptionable_type', 'dentist')
            ->where('subscription_is_valid', true)
            ->whereDate('subscription_from', '<=', Carbon::now())
            ->whereDate('subscription_to', '>=', Carbon::now())
            ->whereHas('subscriptionable', function ($query) {
                $query->where('subscription_is_valid_now', true);
            })
            ->with(['subscriptionable' => function ($query) {
                $query->select("id", "first_name", "last_name", "phone", "address");
            }])->get(
                [
                    'subscriptionable_id', // LabManager or Dentist
                    'subscriptionable_type',
                    'subscription_from',
                    'subscription_to',
                ]
            )
            ->map(function ($subscription) {
                $from = Carbon::parse($subscription->subscription_from);
                $to = Carbon::parse($subscription->subscription_to);
                $subscription->duration_as_months = $from->floatDiffInMonths($to);
                return $subscription;
            });

        // dd($clinics_subscriptions);

        // With Pagination :
        // $clinics_subscriptions = Subscription::where('subscriptionable_type', 'labManager')
        //     // ->where('subscription_is_valid', true)
        //     ->whereDate('subscription_from', '<=', Carbon::now())
        //     ->whereDate('subscription_to', '>=', Carbon::now())
        //     ->orderBy("subscription_to")
        //     ->with(['subscriptionable' => function ($query) {
        //         $query->select('id', "lab_name", 'lab_phone', 'lab_address');
        //     }])->paginate($perPage, [
        //         'subscriptionable_id', // LabManager or Dentist
        //         'subscriptionable_type',
        //         'subscription_from',
        //         'subscription_to',
        //     ]);

        // $clinics_subscriptions->getCollection()->transform(function ($subscription) {
        //     $from = Carbon::parse($subscription->subscription_from);
        //     $to = Carbon::parse($subscription->subscription_to);
        //     $subscription->duration_as_months = $from->floatDiffInMonths($to);
        //     return $subscription;
        // });
        return $clinics_subscriptions;
    }
    public function check_non_subscribers($subscriptionable_type)
    {
        $not_subscribed_labs_or_clinics = Subscription::where('subscriptionable_type', $subscriptionable_type)
            // ->where('subscription_is_valid', true)
            ->whereDate('subscription_to', '<', Carbon::now())->get();
        if ($subscriptionable_type == "labManager") {

            foreach ($not_subscribed_labs_or_clinics as $not_subscribed_lab_subscription) {

                $not_subscribed_lab_subscription->subscriptionable->subscription_is_valid_now == false;
                $not_subscribed_lab_subscription->subscriptionable->save();
            }
        }

        if ($subscriptionable_type == "dentist") {

            foreach ($not_subscribed_labs_or_clinics as $not_subscribed_clinic) {
                $not_subscribed_clinic->subscriptionable->subscription_is_valid_now == false;
                $not_subscribed_clinic->subscriptionable->save();
            }
        }
    }
    public function filterLabs($labName = null, $registerDate = null, $perPage = 10)
    {
        $query = LabManager::subscriptionIsValidNow();
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
        $query = Dentist::subscriptionIsValidNow();
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

    public function getLabsWithNullSubscription(/*$perPage*/)
    {
        $subscriptionable_type = "labManager";
        $this->check_non_subscribers($subscriptionable_type);

        $nonSubscripedLabManagers_ids = LabManager::subscription_NOT_ValidNow()->pluck("id");

        $subscriptions = Subscription::where('subscription_is_valid', 0)
            ->where('subscriptionable_type', $subscriptionable_type)
            ->whereIntegerInRaw('subscriptionable_id', $nonSubscripedLabManagers_ids)
            ->orderByDesc('id') // prioritize by ID first
            ->with(['subscriptionable' => function ($query) {
                $query->select('id', 'lab_name', 'lab_phone', 'lab_address');
            }])
            ->get([
                'subscriptionable_id',
                'subscriptionable_type',
                'subscription_to',
                'id'
            ])
            ->unique('subscriptionable_id') // distinct by this field
            ->sortByDesc('subscription_to') // final sort by date
            ->values();

        // ->paginate($perPage, ['id', 'lab_name', 'lab_phone', 'lab_address', 'register_date']);

        return $subscriptions;
    }
    public function getClinicsWithNullSubscription(/*$perPage*/)
    {
        // $subscriptionable_type = "dentist";
        // $this->check_non_subscribers($subscriptionable_type);

        // $dentists = Dentist::subscription_NOT_ValidNow()
        //     ->paginate($perPage, ['id', 'first_name', 'last_name', 'phone', 'address', 'register_date']);
        // return $dentists->map(function ($dentist) {
        //     $dentist->full_name = $dentist->first_name . ' ' . $dentist->last_name;
        //     unset($dentist->first_name, $dentist->last_name); // لحذف الحقول الأصلية إذا كنت لا تحتاجها
        //     return $dentist;
        // });
        $subscriptionable_type = "dentist";
        $this->check_non_subscribers($subscriptionable_type);

        $nonSubscripedDentists_ids = Dentist::subscription_NOT_ValidNow()->pluck("id");

        $subscriptions = Subscription::where('subscription_is_valid', 0)
            ->where('subscriptionable_type', $subscriptionable_type)
            ->whereIntegerInRaw('subscriptionable_id', $nonSubscripedDentists_ids)
            ->orderByDesc('id') // prioritize by ID first
            ->with(['subscriptionable' => function ($query) {
                $query->select('id', 'first_name', 'last_name', 'phone', 'address');
            }])
            ->get([
                'subscriptionable_id',
                'subscriptionable_type',
                'subscription_to',
                'id'
            ])
            ->unique('subscriptionable_id') // distinct by this field
            ->sortByDesc('subscription_to') // final sort by date
            ->values();

        // ->paginate($perPage, ['id', 'lab_name', 'lab_phone', 'lab_address', 'register_date']);

        return $subscriptions;
    }
    public function getLabsWithRegisterAcceptedZero(/*$perPage*/)
    {
        $register_labs_requests = LabManager::where('register_accepted', NULL)
            ->orderByDesc("created_at")
            // ->with(['subscriptions' => function ($query) {
            //     $query->select(
            //         // 'id',
            //         'subscriptionable_id', // LabManager or Dentist
            //         'subscriptionable_type',
            //         'subscription_from',
            //         'subscription_to'
            //     );
            // }])
            // ->paginate($perPage, ['id', 'lab_name', 'lab_phone', 'lab_address', 'register_date']);
            ->get(['id', 'lab_name', 'lab_phone', 'lab_address', "register_subscription_duration", "created_at"]);
        // calculate months duration subscription
        // ->map(function ($labManager) {
        //     // Get the first subscription, assuming one per LabManager
        //     $subscription = $labManager->subscriptions->first();

        //     if ($subscription && $subscription->subscription_from && $subscription->subscription_to) {
        //         $from = Carbon::parse($subscription->subscription_from);
        //         $to = Carbon::parse($subscription->subscription_to);
        //         $labManager->duration_as_months = $from->floatDiffInMonths($to);
        //     } else {
        //         $labManager->duration_as_months = null;
        //     }

        //     return $labManager;
        // });

        return $register_labs_requests;
    }

    public function getClinicsWithRegisterAcceptedZero(/*$perPage*/)
    {
        $register_labs_requests = Dentist::where('register_accepted', NULL)
            ->orderByDesc("created_at")
            // ->with(['subscriptions' => function ($query) {
            //     $query->select(
            //         // 'id',
            //         'subscriptionable_id', // LabManager or Dentist
            //         'subscriptionable_type',
            //         'subscription_from',
            //         'subscription_to'
            //     );
            // }])
            // ->paginate($perPage, ['id', 'lab_name', 'lab_phone', 'lab_address', 'register_date']);
            ->get(['id', 'first_name', 'last_name', 'phone', 'address', "register_subscription_duration", "created_at"]);
        // calculate months duration subscription
        // ->map(function ($dentist) {
        //     // Get the first subscription, assuming one per dentist
        //     $subscription = $dentist->subscriptions->first();

        //     if ($subscription && $subscription->subscription_from && $subscription->subscription_to) {
        //         $from = Carbon::parse($subscription->subscription_from);
        //         $to = Carbon::parse($subscription->subscription_to);
        //         $dentist->duration_as_months = $from->floatDiffInMonths($to);
        //     } else {
        //         $dentist->duration_as_months = null;
        //     }

        //     return $dentist;
        // });

        return $register_labs_requests;
    }
    public function renewSubscription($subscription_id, $months/*, $subscriptionValue*/)
    {
        // التأكد من تحويل $months إلى عدد صحيح
        $months = intval($months);

        $lastSubscription = Subscription::find($subscription_id);

        if ($lastSubscription->subscription_is_valid == false && $lastSubscription->subscriptionable->subscription_is_valid_now == false) {

            $non_subscriped_duration = Subscription::create([

                'subscriptionable_id' => $lastSubscription->subscriptionable_id,
                'subscriptionable_type' => $lastSubscription->subscriptionable_type,
                'subscription_from' => Carbon::parse($lastSubscription->subscription_to)->addDay()->toDateString(),
                'subscription_to' => Carbon::now()->subDay()->toDateString(),
                'subscription_is_valid' => false,
                'subscription_value' => null

            ]);

            $renew_subscribing = Subscription::create([

                'subscriptionable_id' => $lastSubscription->subscriptionable_id,
                'subscriptionable_type' => $lastSubscription->subscriptionable_type,
                'subscription_from' => now(),
                'subscription_to' => Carbon::now()->addMonths($months)->toDateString(),
                'subscription_is_valid' => true,
                'subscription_value' => 0

            ]);

            // تحديث القيمة subscription_is_valid_now في جدول lab_managers
            $renew_subscribing->subscriptionable->update(['subscription_is_valid_now' => true]);
        }
    }


    public function updateRegisterAccepted($id)
    {
        $labManager = LabManager::find($id);

        if ($labManager->subscription_is_valid_now == true) {
            return false;
        }

        if ($labManager && $labManager->subscription_is_valid_now == null) {
            $labManager->register_accepted = 1;
            $labManager->register_date = now();
            $labManager->subscription_is_valid_now = true;
            $labManager->save();

            $labSubscription = Subscription::create([
                'subscriptionable_id' => $id,
                'subscriptionable_type' => "labManager",
                'subscription_from' => now(),
                'subscription_to' => Carbon::now()->addMonths($labManager->register_subscription_duration)->toDateString(),
                'subscription_is_valid' => true,
                'subscription_value' => 0,

            ]);
            // $labSubscription = Subscription::where("subscriptionable_id", $id)
            //     ->where("subscriptionable_type", "labManager")
            //     ->whereNull("subscription_is_valid")
            //     ->first();

            // if ($labSubscription && now()->gt($labSubscription->subscription_to)) {
            //     return NULL;
            // }
            // if ($labSubscription && now()->lt($labSubscription->subscription_from)) {
            //     return NULL;
            // }

            // if ($labSubscription && now()->between($labSubscription->subscription_from, $labSubscription->subscription_to)) {
            //     $labManager->register_accepted = 1;
            //     $labManager->register_date = now();
            //     $labManager->subscription_is_valid_now = true;
            //     $labManager->save();
            //     $labSubscription->update([
            //         'subscription_is_valid' => true,
            //         'subscription_value' => 0
            //     ]);

            //Send Welcome Mail
            $MailController = new MailController();
            $MailController->send_welcome_mail($labManager->email, "LabManager");

            return 'yes';
        }
        // }

        return NULL;
    }
    public function updateRegisterAcceptedclinic($id)
    {
        $dentist = Dentist::find($id);

        if ($dentist->subscription_is_valid_now == true) {
            return false;
        }

        if ($dentist && $dentist->subscription_is_valid_now == null) {
            $dentist->register_accepted = 1;
            $dentist->register_date = now();
            $dentist->subscription_is_valid_now = true;
            $dentist->save();
            $clinicSubscription = Subscription::create([
                'subscriptionable_id' => $id,
                'subscriptionable_type' => "dentist",
                'subscription_from' => now(),
                'subscription_to' => Carbon::now()->addMonths($dentist->register_subscription_duration)->toDateString(),
                'subscription_is_valid' => true,
                'subscription_value' => 0,

            ]);
            // $clinicSubscription = Subscription::where("subscriptionable_id", $id)
            //     ->where("subscriptionable_type", "labManager")
            //     ->whereNull("subscription_is_valid")
            //     ->first();

            // if ($clinicSubscription && now()->gt($clinicSubscription->subscription_to)) {
            //     return NULL;
            // }
            // if ($clinicSubscription && now()->lt($clinicSubscription->subscription_from)) {
            //     return NULL;
            // }

            // if ($clinicSubscription && now()->between($clinicSubscription->subscription_from, $clinicSubscription->subscription_to)) {
            //     $labManager->register_accepted = 1;
            //     $labManager->register_date = now();
            //     $labManager->subscription_is_valid_now = true;
            //     $labManager->save();
            //     $clinicSubscription->update([
            //         'subscription_is_valid' => true,
            //         'subscription_value' => 0
            //     ]);

            //Send Welcome Mail
            $MailController = new MailController();
            $MailController->send_welcome_mail($dentist->email, "Dentist");

            return 'yes';
        }
        // }

        return NULL;
    }
}
