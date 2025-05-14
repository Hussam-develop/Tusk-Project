<?php

namespace Database\Seeders;

use App\Models\Accountant;
use App\Models\InventoryEmployee;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Admin;
use App\Models\Dentist;
use App\Models\Secretary;
use App\Models\LabManager;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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
            'password' => Hash::make('password'),

        ]);
        for ($i = 1; $i <= 5; $i++) {
            Dentist::create([
                'first_name' => "dentist" . $i,
                'last_name' => "dentist" . $i,
                'phone' => "0987070814",
                'email' => "dentist$i" . "@gmail.com",
                // 'type' => "admin",
                // 'register_accepted' => true,
                'email_is_verified' => true,
                'verification_code' => 55555,
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'address' => "سوق الحميدية", //clinic address
                'image_path' => 'image_path',
                'register_accepted' => true,
                'province' => "Damascus",
                // 'remember_token' => '',
                'register_date' => now(),
                'subscription_is_valid_now' => 1,
            ]);
        }
        for ($i = 1; $i <= 5; $i++) {
            LabManager::create([
                'first_name' => "manager" . $i,
                'last_name' => "manager" . $i,
                // 'type' => "admin",
                // 'register_accepted' => true,
                'email' => "manager$i" . "@gmail.com",
                'email_is_verified' => true,
                'verification_code' => 55555,
                'email_verified_at' => now(),
                'password' => Hash::make('password'),

                'register_accepted' => true,
                // 'remember_token' => '',

                'lab_name' => "lab$i",
                'lab_address' => "alzaheraa",
                'lab_province' => "Damascus",
                'lab_phone' => json_encode([
                    'home' => fake()->phoneNumber(),
                    'work' => fake()->phoneNumber(),
                    'mobile' => fake()->phoneNumber(),
                ]),

                'register_date' => now(),
                'subscription_is_valid_now' => 1,
                'lab_logo' => "logo_path",
                'lab_type' => "teeth",
                'lab_from_hour' => "08:00",
                'lab_to_hour' => "05:00",
            ]);
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
            for ($i = 1; $i <= 2; $i++) {
                Accountant::create([
                    'lab_manager_id' => $lab_manager->id,
                    'first_name' => "المحاسب $i",
                    'last_name' => "الشاطر",
                    // 'address' => "damascus",
                    'is_staged' => false,
                    'password' => Hash::make('password'),
                    'phone' => "0999999999",
                    'email' => "accountant$i.$lab_manager->id@gmail.com",
                    'remember_token' => "",
                    'email_is_verified' => 0,
                    'email_verified_at' => null,
                    'verification_code' => null,

                ]);
            }

            for ($i = 1; $i <= 2; $i++) {
                InventoryEmployee::create([
                    'lab_manager_id' => $lab_manager->id,
                    'first_name' => "موظف المخزون $i",
                    'last_name' => "الشاطر",
                    // 'address' => "damascus",
                    'is_staged' => false,
                    'password' => Hash::make('password'),
                    'phone' => "0999999999",
                    'email' => "inventory.employee$i.$lab_manager->id@gmail.com",
                    'remember_token' => "",
                    'email_is_verified' => 0,
                    'email_verified_at' => null,
                    'verification_code' => null,

                ]);
            }
        }
    }
}
