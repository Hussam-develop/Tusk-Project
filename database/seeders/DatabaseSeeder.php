<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Admin;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Dentist;
use App\Models\Secretary;
use App\Models\Accountant;
use App\Models\DoctorTime;
use App\Models\LabManager;
use App\Models\Subscription;
use Illuminate\Database\Seeder;
use Database\Seeders\BillSeeder;
use Database\Seeders\itemSeeder;
use App\Models\InventoryEmployee;
use Database\Seeders\PatientSeeder;
use Database\Seeders\CategorySeeder;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\TreatmentSeeder;
use Database\Seeders\itemhistoryseeder;
use Database\Seeders\SubCategorySeeder;
use Database\Seeders\OperatingPaymentSeeder;
use Database\Seeders\DentistLabManagerSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        Admin::create([
            'first_name' => "admin",
            'last_name' => "admin",
            'phone' => "0987070814",
            // 'type' => "admin",
            // 'register_accepted' => true,
            'email' => "admin@gmail.com",
            'password' => Hash::make('password')

        ]);
        for ($i = 1; $i <= 5; $i++) {
            Dentist::create([
                'first_name' => "dentist" . $i,
                'last_name' => "dentist" . $i,
                'phone' => "0987070814",
                'email' => "dentist$i" . "@gmail.com",

                // 'work_from_hour' => "09:00",
                // 'work_to_hour' => "20:00",

                // 'type' => "admin",
                // 'register_accepted' => true,
                'email_is_verified' => true,
                'verification_code' => 55555,
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                "register_subscription_duration" => 3,
                'address' => "سوق الحميدية", //clinic address
                'image_path' => 'image_path',
                'register_accepted' => null,

                // 'remember_token' => '',
                'register_date' => now(),
                'subscription_is_valid_now' => null,
            ]);
            // $registerSubscription = Subscription::create([
            //     'subscriptionable_id' => $i, // LabManager or Dentist
            //     'subscriptionable_type' => "dentist",
            //     'subscription_from' => now(),
            //     'subscription_to' => Carbon::now()->addMonths(2)->toDateString(),
            //     'subscription_is_valid' => null,
            //     'subscription_value' => null,
            // ]);
        }
        $days = ["السبت", "الأحد", "الاثنين", "الثلاثاء", "الأربعاء", "الخميس", "الجمعة"];

        for ($i = 1; $i <= 5; $i++) {
            foreach ($days as $day) {
                DoctorTime::create([
                    "dentist_id" => $i,
                    "day" => $day,
                    "start_time" => "08:00",
                    "end_time" => "18:00",
                    "start_rest" => "13:00",
                    "end_rest" => "15:00",
                    "created_at" => now(),
                    "updated_at" => now()

                ]);
            }
        }
        for ($i = 1; $i <= 5; $i++) {
            LabManager::create([
                'full_name' => "manager" . $i,
                // 'type' => "admin",
                // 'register_accepted' => true,
                'email' => "manager$i" . "@gmail.com",
                'email_is_verified' => true,
                'verification_code' => 55555,
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                "register_subscription_duration" => 3,

                'register_accepted' => null,
                // 'remember_token' => '',

                'lab_name' => "lab$i",
                'lab_address' => "alzaheraa",
                'lab_province' => "Damascus",
                'lab_phone' => json_encode([
                    '1' => fake()->phoneNumber(),
                    '2' => fake()->phoneNumber(),
                    '3' => fake()->phoneNumber(),
                ]),

                'register_date' => now(),
                // 'subscription_is_valid_now' => (bool)rand(0, 1),
                'subscription_is_valid_now' => null,
                'lab_logo' => "logo_path",
                'lab_type' => "teeth",
                'work_from_hour' => "09:00",
                'work_to_hour' => "20:00",
            ]);
            // $registerSubscription = Subscription::create([
            //     'subscriptionable_id' => $i, // LabManager or Dentist
            //     'subscriptionable_type' => "labManager",
            //     'subscription_from' => now(),
            //     'subscription_to' => Carbon::now()->addMonths(3)->toDateString(),
            //     'subscription_is_valid' => null,
            //     'subscription_value' => null,
            // ]);
        }

        $dentists = Dentist::all();
        foreach ($dentists as $dentist) {
            for ($i = 1; $i <= 2; $i++) {
                Secretary::create([
                    'dentist_id' => $dentist->id,
                    'first_name' => "s$i",
                    'last_name' => "k$i",
                    'address' => "damascus",
                    'is_staged' => false,
                    'password' => Hash::make('password'),
                    'attendence_time' => "day",
                    'phone' => "0999999999",
                    'email' => "s$i.$dentist->id@gmail.com",
                    'remember_token' => "",
                    'email_is_verified' => 1,
                    'email_verified_at' => now(),
                    'verification_code' => null,

                ]);
            }
        }
        $labManager = LabManager::all();
        foreach ($labManager as $lab_manager) {
            for ($i = 1; $i < 2; $i++) {
                Accountant::create([
                    'lab_manager_id' => $lab_manager->id,
                    'full_name' => "المحاسب $lab_manager->id.$i",
                    // 'address' => "damascus",
                    'is_staged' => false,
                    'password' => Hash::make('password'),
                    'phone' => "0999999999",
                    'email' => "accountant" . $lab_manager->id . "." . $i . "@gmail.com",
                    'remember_token' => "",
                    'email_is_verified' => 0,
                    'active' => 1,
                    'email_verified_at' => now(),
                    'verification_code' => 555555,
                    'work_start_at' => now()
                ]);
            }
            for ($i = 2; $i <= 5; $i++) {
                Accountant::create([
                    'lab_manager_id' => $lab_manager->id,
                    'full_name' => "المحاسب $lab_manager->id.$i",
                    // 'address' => "damascus",
                    'is_staged' => false,
                    'password' => Hash::make('password'),
                    'phone' => "0999999999",
                    'email' => "accountant" . $lab_manager->id . "." . $i . "@gmail.com",
                    'remember_token' => "",
                    'email_is_verified' => 0,
                    'active' => 0,
                    'email_verified_at' => now(),
                    'verification_code' => 555555,
                    'work_start_at' => now()
                ]);
            }

            for ($i = 1; $i < 2; $i++) {
                InventoryEmployee::create([
                    'lab_manager_id' => $lab_manager->id,
                    'full_name' => "موظف المخزون $lab_manager->id.$i",
                    // 'address' => "damascus",
                    'is_staged' => false,
                    'password' => Hash::make('password'),
                    'phone' => "0999999999",
                    'email' => "inventory_employee" . $lab_manager->id . "." . $i . "@gmail.com",
                    'remember_token' => "",
                    'email_is_verified' => 0,
                    'active' => 1,
                    'email_verified_at' => now(),
                    'verification_code' => 555555,
                    'work_start_at' => now()

                ]);
            }
            for ($i = 2; $i <= 5; $i++) {
                InventoryEmployee::create([
                    'lab_manager_id' => $lab_manager->id,
                    'full_name' => "موظف المخزون $lab_manager->id.$i",
                    // 'address' => "damascus",
                    'is_staged' => false,
                    'password' => Hash::make('password'),
                    'phone' => "0999999999",
                    'email' => "inventory_employee" . $lab_manager->id . "." . $i . "@gmail.com",
                    'remember_token' => "",
                    'email_is_verified' => 0,
                    'active' => 0,
                    'email_verified_at' => now(),
                    'verification_code' => 555555,
                    'work_start_at' => now()

                ]);
            }
        }

        // $this->call([
        //     DentistLabManagerSeeder::class,
        //     itemhistoryseeder::class,
        //     OperatingPaymentSeeder::class,
        //     PatientSeeder::class,
        //     TreatmentSeeder::class,
        //     accountrecord_seeder::class,
        //     BillSeeder::class,
        //     CategorySeeder::class,
        //     SubCategorySeeder::class,
        //     itemSeeder::class,
        // ]);
    }
}
