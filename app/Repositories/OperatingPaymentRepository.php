<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Models\Treatment;
use App\Models\ItemHistory;
use App\Models\OperatingPayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OperatingPaymentRepository
{

    public function allOperatingPayment($user_id, $type)
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $previousMonth = $currentMonth - 1;
        $previousYear = $currentYear;

        // إذا الشهر الحالي هو يناير، فالشهر السابق هو ديسمبر من السنة السابقة
        if ($previousMonth == 0) {
            $previousMonth = 12;
            $previousYear -= 1;
        }

        // استعلام لجمع القيم
        $totalValue = OperatingPayment::whereYear('created_at', $previousYear)
            ->whereMonth('created_at', $previousMonth)
            ->where('creatorable_type', $type)
            ->where('creatorable_id', $user_id)
            ->sum('value');

        return $totalValue;
    }
    public function Operating_Payment_statistics($user_id, $type)
    {
        if ($type != 'dentist' && $type != 'labManager') {
            return 'ليس طبيب او مدير مخبر';
        }

        // تحديد الشهر الحالي وشهر السابق
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // تحديد الشهر السابق
        $previousMonth = $currentMonth - 1;
        $previousYear = $currentMonth === 1 ? $currentYear - 1 : $currentYear;

        // استعلام لجلب البيانات مع عدد مرات الظهور لكل اسم
        $results = OperatingPayment::select(
            'name',
            DB::raw('SUM(value) as total_value'),
            DB::raw('COUNT(*) as count')
        )
            ->whereYear('created_at', $previousYear)
            ->whereMonth('created_at', $previousMonth)
            ->where('creatorable_type', $type)
            ->where('creatorable_id', $user_id)
            ->groupBy('name')
            ->get();

        return $results;
    }
    function doctor_gains_statistics()
    {
        $dentist_id = auth()->user()->id;
        // dd($dentist_id);

        /**
        1. ( not needed ) AccountRecord at all labs : just doctorAccounts (without bills)  ⚡ this is occoured in Bills not condidering the doctorAccount
        2. Bills ( or billCases or MedicalCases ) ⚡
        3. Item histories & Items (just when buy items without consuming ) ⚡
        4. Operating Payments ⚡
        5. ( not needed ) Patients and\or Patients Payments ⚡ this is occoured in Treatments where is_paid = true
        6. ( not needed ) Subscription ⚡
        7. Treatments ⚡

         */

        // Define the start date (last 1 year)
        $startDate = Carbon::now()->subYear()->startOfMonth();

        // Income Query: Sum of `cost` from `Treatment` model
        $income = DB::table('treatments')
            ->selectRaw('MONTH(created_at) as month_number, SUM(cost) as income')
            ->where('dentist_id', $dentist_id)
            ->where('is_paid', 1)
            ->where('created_at', '>=', $startDate)
            ->groupByRaw('MONTH(created_at)')
            ->get();
        // dd($income);
        // Outcome Query (Part 1): Sum of `signed_value` from `AccountRecord` model
        // $accountOutcome = DB::table('account_records')
        //     ->selectRaw('MONTH(created_at) as month_number, SUM(signed_value) as outcome')
        //     ->where('dentist_id', $dentist_id)
        //     ->whereNull('bill_id')
        //     ->where('created_at', '>=', $startDate)
        //     ->groupByRaw('MONTH(created_at)')
        //     ->get();

        // Outcome Query (Part 2): Sum of `value` from `OperatingPayment` model
        $paymentOutcome = DB::table('operating_payments')
            ->selectRaw('MONTH(created_at) as month_number, SUM(value) as outcome')
            ->where('creatorable_id', $dentist_id)
            ->where('creatorable_type', 'dentist')
            ->where('created_at', '>=', $startDate)
            ->groupByRaw('MONTH(created_at)')
            ->get();

        // Outcome Query (Part 3): Sum of `total_cost` from `Bill` model
        $billsOutcome = DB::table('bills')
            ->selectRaw('MONTH(created_at) as month_number, SUM(total_cost) as outcome')
            ->where('dentist_id', $dentist_id)
            ->where('created_at', '>=', $startDate)
            ->groupByRaw('MONTH(created_at)')
            ->get();

        // Outcome Query (Part 4): Sum of `total_price` from `ItemHistory` model
        // $itemHistoriesOutcome = DB::table('item_histories')
        //     ->selectRaw('MONTH(created_at) as month_number, SUM(total_price) as outcome')
        //     // ->where('dentist_id', $dentist_id)
        //     ->where('quantity', '>', 0)
        //     ->where('created_at', '>=', $startDate)
        //     ->groupByRaw('MONTH(created_at)')
        //     ->get();

        // Outcome Query (Part 4): Sum of `total_price` from `ItemHistory` model
        $itemHistoriesOutcome = ItemHistory::selectRaw('MONTH(created_at) as month_number, SUM(total_price) as outcome')
            ->whereHas('item', function ($query) use ($dentist_id) {
                $query->where('creatorable_id', $dentist_id)
                    ->where('creatorable_type', 'dentist');
            })
            ->where('quantity', '>', 0)
            ->where('created_at', '>=', $startDate)
            ->groupByRaw('MONTH(created_at)')
            ->get();
        // Merge income and outcome data by month
        $data = [];

        for ($i = 1; $i <= 12; $i++) {

            $incomeValue = optional($income->where('month_number', $i)->first())->income ?? 0;

            // $accountOutcomeValue = optional($accountOutcome->where('month_number', $i)->first())->outcome ?? 0;
            $operatingPaymentOutcomeValue = optional($paymentOutcome->where('month_number', $i)->first())->outcome ?? 0;
            $billsOutcomeValue = optional($billsOutcome->where('month_number', $i)->first())->outcome ?? 0;
            $itemHistoriesOutcomeValue = optional($itemHistoriesOutcome->where('month_number', $i)->first())->outcome ?? 0;

            $totalOutcome = $operatingPaymentOutcomeValue + $billsOutcomeValue + $itemHistoriesOutcomeValue;
            // + $accountOutcomeValue
            $gain = $incomeValue - $totalOutcome;

            $data[] = [
                'month_number' => $i,
                'income' => $incomeValue,
                'outcome' => $totalOutcome,
                'gain' => $gain
            ];
        }

        return $data;
    }


    // public function doctor_gains_statistics00()
    // {
    //     $income = [
    //         "1" => [
    //             "tropical" => ["mango" => "yellow", "banana" => "yellow"],
    //             "berries" => ["strawberry" => "red", "blueberry" => "blue"],
    //         ],
    //         "2" => [
    //             "pets" => ["dog" => "bark", "cat" => "meow"],
    //             "wild" => ["lion" => "roar", "elephant" => "trumpet"],
    //         ]
    //     ];
    //     $outcome = [
    //         "fruits" => [
    //             "tropical" => ["mango" => "yellow", "banana" => "yellow"],
    //             "berries" => ["strawberry" => "red", "blueberry" => "blue"],
    //         ],
    //         "animals" => [
    //             "pets" => ["dog" => "bark", "cat" => "meow"],
    //             "wild" => ["lion" => "roar", "elephant" => "trumpet"],
    //         ]
    //     ];

    //     $treatments = Treatment::where("dentist_id", 5)
    //         ->where('is_paid', 1);

    //     // $resultsCollection = collect();

    //     // $stats = Treatment::selectRaw('MONTH(created_at) as month, COUNT(*) as total_patients')
    //     //     ->where('dentist_id', $dentist_id)
    //     //     ->groupBy('month')
    //     //     ->get();

    //     // foreach ($stats as $stat) {
    //     //     $resultsCollection->push([
    //     //         'month' => $stat->month,
    //     //         'patient_count' => $stat->total_patients,
    //     //     ]);
    //     // }

    //     // الآن النتائج مخزنة في مجموعة $resultsCollection
    //     return $treatments;
    //     // return $resultsCollection;
    // }
    public function getAll()
    {
        $user = auth()->user();
        $user_type = $user->getMorphClass();
        if ($user_type == "dentist") {
            $operatingPayment =  OperatingPayment::where("creatorable_id", $user->id)
                ->where("creatorable_id", $user->id)
                ->where("creatorable_type", $user_type)
                ->orderBy('id', 'desc')
                ->get();
        }
        if ($user_type == "labManager") {
            $accountantId = $user->accountant->id;
            $operatingPayment =  OperatingPayment::where("creatorable_id", $user->id)
                ->where([
                    ["creatorable_id", $user->id],
                    ["creatorable_type", "labManager"]
                ])->orWhere([
                    ["creatorable_id", $accountantId],
                    ["creatorable_type", "accountant"]
                ])
                ->orderBy('id', 'desc')
                ->get();
        }
        if ($user_type == "accountant") {

            $labManagerId = $user->labManager->id;
            $operatingPayment =  OperatingPayment::where("creatorable_id", $user->id)
                ->where([
                    ["creatorable_id", $user->id],
                    ["creatorable_type", "accountant"]
                ])->orWhere([
                    ["creatorable_id", $labManagerId],
                    ["creatorable_type", "labManager"]
                ])
                ->orderBy('id', 'desc')
                ->get();
        }
        return $operatingPayment;
    }

    public function getById($id)
    {
        return OperatingPayment::findOrFail($id);
    }

    public function getPaginate($perPage = 10)
    {
        return OperatingPayment::paginate($perPage);
    }

    public function create($request)
    {
        $user = auth()->user();
        $user_type = $user->getMorphClass();

        $operatingPayment = OperatingPayment::create([

            'creatorable_id' => $user->id,
            'creatorable_type' => $user_type,

            'name' => $request->name,
            'value' => $request->value,
        ]);
        if ($operatingPayment) {
            return true;
        }
        return false;
    }

    public function update($id, array $data)
    {
        $item = OperatingPayment::findOrFail($id);
        $item->update($data);
        return $item;
    }

    public function delete($id)
    {
        return OperatingPayment::destroy($id);
    }
}
