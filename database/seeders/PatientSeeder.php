<?php

namespace Database\Seeders;

use App\Models\Patient;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PatientSeeder extends Seeder
{
    public function run()
    {
        $patients = [
            // شهر 1
            [
                'dentist_id' => 1,
                'full_name' => 'wisam',

                'phone' => '0100000001',
                'birthday' => '1985-03-15',
                'current_balance' => 1000,
                'is_smoker' => false,
                'address' => 'Cairo, Egypt',
                'gender' => 'male',
                'created_at' => '2024-01-05 10:00:00',
                'updated_at' => '2024-01-05 10:00:00'
            ],
            [
                'dentist_id' => 2,
                'full_name' => 'samerr',

                'phone' => '0100000002',
                'birthday' => '1990-07-20',
                'current_balance' => 500,
                'is_smoker' => false,
                'address' => 'Alexandria, Egypt',
                'gender' => 'female',
                'created_at' => '2024-01-10 11:00:00',
                'updated_at' => '2024-01-10 11:00:00'
            ],
            // ... أضف 8 مرضى آخرين لنفس الشهر

            // شهر 2
            [
                'dentist_id' => 3,
                'full_name' => 'lamis',

                'phone' => '0100000011',
                'birthday' => '1982-12-12',
                'current_balance' => 200,
                'is_smoker' => true,
                'address' => 'Giza, Egypt',
                'gender' => 'male',
                'created_at' => '2024-02-15 09:30:00',
                'updated_at' => '2024-02-15 09:30:00'
            ],
            // ... أضف 7 مرضى آخرين لنفس الشهر

            // شهر 3
            [
                'dentist_id' => 4,
                'full_name' => 'iman',

                'phone' => '0100000022',
                'birthday' => '1995-05-05',
                'current_balance' => 750,
                'is_smoker' => false,
                'address' => 'Mansoura, Egypt',
                'gender' => 'female',
                'created_at' => '2024-03-10 14:00:00',
                'updated_at' => '2024-03-10 14:00:00'
            ],
            // ... أضف 7 مرضى آخرين لنفس الشهر

            // شهر 4
            [
                'dentist_id' => 5,
                'full_name' => 'fatima',

                'phone' => '0100000033',
                'birthday' => '1978-09-09',
                'current_balance' => 300,
                'is_smoker' => true,
                'address' => 'Aswan, Egypt',
                'gender' => 'male',
                'created_at' => '2024-04-20 16:45:00',
                'updated_at' => '2024-04-20 16:45:00'
            ],
            // ... أضف 7 مرضى آخرين لنفس الشهر
        ];

        // يمكنك تكرار أو تحديث البيانات حسب الحاجة

        foreach ($patients as $patient) {
            Patient::create($patient);
        }
    }
}
