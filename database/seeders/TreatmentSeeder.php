<?php

namespace Database\Seeders;

use App\Models\Treatment;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TreatmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // عدد المعالجات المراد إنشاؤها
        $totalTreatments = 40;

        // تحديد أنواع العلاج
        $types = ['سحب عصب', 'قلع جراحي', 'ترميم'];

        // تحديد تاريخ البداية
        $startDate = Carbon::now()->subMonths(4);

        for ($i = 0; $i < $totalTreatments; $i++) {
            // تحديد الشهر بشكل دوري من 1 إلى 12
            $monthOffset = ($i % 12);
            $date = $startDate->copy()->addMonths($monthOffset);

            // اختيار نوع عشوائي من الأنواع
            $typeIndex = array_rand($types);
            $currentType = $types[$typeIndex];

            // إنشاء سجل العلاج
            Treatment::create([
                'patient_id' => 1,
                'dentist_id' => 1,
                'medical_case_id' => null,
                'cost' => rand(300, 1000), // قيمة عشوائية بين 300 و 1000
                'type' => $currentType,
                'details' => 'تفاصيل العلاج',
                'date' => $date,
                'is_paid' => rand(0, 1) == 1 ? true : false, // حالة الدفع عشوائية
            ]);
        }
    }
}
