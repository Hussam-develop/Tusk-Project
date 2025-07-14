<?php

namespace App\Repositories;

use App\Models\Bill;
use App\Models\Dentist;
use App\Models\BillCase;
use App\Models\DentistLabManager;
use App\Models\LabManager;
use App\Models\MedicalCase;
use Illuminate\Http\Request;
use App\Models\AccountRecord;
use App\Http\Requests\BillRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BillRepository
{
    public function get_lab_bills_descending_as_dentist($lab_id)
    {
        $lab_bills = Bill::where("dentist_id", auth()->user()->id)
            ->where("lab_manager_id", $lab_id)
            ->orderByDesc("created_at")
            ->get(['id', 'total_cost', 'date_from', 'date_to', 'created_at']);
        $lab_name = LabManager::where("id", $lab_id)
            ->first(["id", "lab_name"]);

        return [
            'lab' => $lab_name,
            'bills' => $lab_bills
        ];
    }
    public function show_bill_details_with_cases_as_dentist($bill_id)
    {
        $bill = Bill::findOrFail($bill_id);
        // $bill_cases = $bill->billCases->with('patient:id,full_name')->get();
        $bill_cases = BillCase::where('bill_id', $bill->id)
            ->get();
        // $bill_cases = $bill->billCases
        //     ->with(['patient:id,full_name']);
        // ->get(['cost', 'expected_delivery_date', 'created_at']);

        $bill_cases_with_patient = DB::table('bill_cases')
            ->join('medical_cases', 'bill_cases.medical_case_id', '=', 'medical_cases.id')
            ->join('patients', 'medical_cases.patient_id', '=', 'patients.id')
            ->where('bill_cases.bill_id', '=', $bill_id) // Filter by bill_id
            ->select(
                'bill_cases.id',
                'bill_cases.bill_id',
                'bill_cases.medical_case_id',
                'bill_cases.case_cost',
                'bill_cases.created_at',
                'medical_cases.patient_id',
                'medical_cases.expected_delivery_date',
                // 'medical_cases.cost',
                'medical_cases.created_at',
                'patients.id as patient_id',
                'patients.full_name'
            )
            ->orderBy('medical_cases.expected_delivery_date', 'asc') // Sort ascending
            ->get();

        return [
            'bill' => $bill,
            'bill_cases' => $bill_cases_with_patient,
        ];
    }
    public function show_lab_bills()
    {
        $user = Auth::user();
        $userType = $user->getMorphClass();

        $lab_id = $userType == 'labManager'
            ? Auth::id() // labManager
            : Auth::user()->labManager->id; // accountant

        $lab_bills = Bill::where("lab_manager_id", $lab_id)
            ->orderByDesc("created_at")
            ->with(['dentist' => function ($query) {
                $query->select('id', "first_name", 'last_name');
            }])
            ->get(["id", "bill_number", "total_cost", "dentist_id", "created_at"]);

        if ($lab_bills->isEmpty()) {
            return [
                "message" => "لا توجد فواتير لعرضها بعد",
                "message_status" => "error"
            ];
        }
        if ($lab_bills) {

            return [
                "data" => $lab_bills,
                "message" => "فواتير المخبر",
                "message_status" => "done"
            ];
        }
        return [
            "message" => "حدث خطأ أثناء عرض فواتير المخبر"
        ];
    }
    public function show_dentist_bills($dentist_id)
    {
        $user = Auth::user();
        $userType = $user->getMorphClass();

        $lab_id = $userType == 'labManager'
            ? Auth::id() // labManager
            : Auth::user()->labManager->id; // accountant

        $dentist_bills = Bill::where("lab_manager_id", $lab_id)
            ->where('dentist_id', $dentist_id)
            ->orderByDesc("created_at")
            ->get(["id", "bill_number", "total_cost", "created_at"]);

        $dentist = Dentist::where("id", $dentist_id)
            ->first([
                'id',
                'first_name',
                'last_name',
                // 'email',
                'phone',
                'address',
            ]);

        $dentist_current_balance = AccountRecord::where(
            [
                'dentist_id' => $dentist_id,
                'lab_manager_id' => $lab_id
            ]
        )->latest()->first("current_account");

        if ($dentist_bills->isEmpty()) {
            return [
                "message" => "لا توجد فواتير لهذا الطبيب حاليا",
                "message_status" => "error"
            ];
        }

        if ($dentist_bills) {

            return [
                "data" => [
                    "dentist_bills" => $dentist_bills,
                    "dentist" => $dentist,
                    "dentist_current_balance" => $dentist_current_balance
                ],
                "message" => "فواتير الطبيب",
                "message_status" => "done",


            ];
        }
        return [
            "message" => "حدث خطأ أثناء عرض فواتير الطبيب"
        ];
    }
    public function show_bill_details($bill_id)
    {
        $user = Auth::user();
        $userType = $user->getMorphClass();

        $lab_id = $userType == 'labManager'
            ? Auth::id() // labManager
            : Auth::user()->labManager->id; // accountant

        $bill = Bill::find($bill_id);
        $bill_cases = BillCase::where('bill_id', $bill_id)
            ->with([
                'medicalCase' => function ($query) {
                    $query->select('id', 'patient_id', 'cost', 'created_at')
                        ->with(['patient:id,full_name']); // eager load only necessary columns
                }
            ])
            ->orderByDesc('created_at')
            ->get();


        if ((!$bill->exists()) || $bill_cases->isEmpty()) {
            return [
                "message" => "حدث خطأ أثناء عرض نفاصيل الفاتورة",
                "message_status" => "error"
            ];
        }

        if ($bill->exists() && $bill_cases->isNotEmpty()) {

            return [
                "data" => [
                    "bill" => $bill,
                    "bill_cases" => $bill_cases,
                ],
                "message" => "تفاصيل الفاتورة",
                "message_status" => "done",


            ];
        }
        return [
            "message" => "حدث خطأ أثناء عرض تفاصيل الفاتورة"
        ];
    }
    public function preview_bill(BillRequest $request)
    {
        $user = Auth::user();
        $userType = $user->getMorphClass();

        $lab_id = $userType == 'labManager'
            ? Auth::id() // labManager
            : Auth::user()->labManager->id; // accountant

        // dd($userType, $lab_id);

        $from = Carbon::parse($request->date_from)->startOfDay();
        $to = Carbon::parse($request->date_to)->endOfDay();

        $states_between_dates = MedicalCase::where("lab_manager_id", $lab_id)
            ->where("dentist_id", $request->dentist_id)
            ->with(['patient:id,full_name']) // eager load only necessary columns
            ->whereBetween('created_at', [$from, $to])
            ->orderBy('created_at', 'asc')
            ->get(["id", "patient_id", "dentist_id", "lab_manager_id", "expected_delivery_date", "status", "cost", "created_at"]);

        if ($states_between_dates->isEmpty()) {
            return [
                "message" => "لا توجد حالات بين هذين التاريخين، يرجى تغيير تاريخ بداية الفاتورة أو نهايتها للمعاينة",
                "message_status" => "error"
            ];
        }
        $all_bill_cases_ids = BillCase::pluck("medical_case_id")->toArray();

        $total_cost = 0;

        foreach ($states_between_dates as $state) {
            $patient_name = $state->patient->full_name;
            $state_date = new Carbon($state->created_at);
            $formatted_state_date = $state_date->format(" Y-m-d ");

            if ($state->cost == 0 || $state->status < 3 /*ready*/) {

                return [
                    "message" => "حالة المريض " . $patient_name . " بتاريخ " . $formatted_state_date . " لم يتم تحديد تكلفة لها بعد " . " ( ليست جاهزة بعد )",
                    "message_status" => "error"
                ];
            }

            if (in_array($state->id, $all_bill_cases_ids)) // OR : Arr::exists($all_bill_cases_ids, $state->id )
            {

                return [
                    "message" => " حالة المريض " . $patient_name . " بتاريخ " . $formatted_state_date .
                        " موجودة بفاتورة سابقة . الرجاء إدخال تاريخ البداية بعد هذا التاريخ ",
                    "message_status" => "error"
                ];
            }
            $total_cost += $state->cost;
        }

        if ($states_between_dates->isNotEmpty()) {

            return [
                "data" => [
                    "medical_cases" => $states_between_dates,
                    "total_bill_cost" => $total_cost,
                ],
                "message" => "معاينة تفاصيل الفاتورة",
                "message_status" => "done",


            ];
        }
        return [
            "message" => "حدث خطأ أثناء معاينة تفاصيل الفاتورة",
            "message_status" => "error"
        ];
    }

    public function addBill(BillRequest $request)
    {
        $user = Auth::user();
        $userType = $user->getMorphClass();

        $lab_id = $userType == 'labManager'
            ? Auth::id() // labManager
            : Auth::user()->labManager->id; // accountant

        // dd($userType, $lab_id);

        $from = Carbon::parse($request->date_from)->startOfDay();
        $to = Carbon::parse($request->date_to)->endOfDay();

        $states_between_dates = MedicalCase::where("lab_manager_id", $lab_id)
            ->where("dentist_id", $request->dentist_id)
            ->whereBetween('created_at', [$from, $to])
            ->get();

        if ($states_between_dates->isEmpty() === true) {
            return "لا توجد حالات بين هذين التاريخين، يرجى تغيير تاريخ بداية الفاتورة أو نهايتها";
        }

        $all_bill_cases_ids = BillCase::pluck("medical_case_id")->toArray();

        foreach ($states_between_dates as $state) {
            $patient_name = $state->patient->full_name;
            $state_date = new Carbon($state->created_at);
            $formatted_state_date = $state_date->format(" Y-m-d ");

            if ($state->cost == 0) {
                // dd($state->id);
                return "حالة المريض " . $patient_name . " بتاريخ " . $formatted_state_date . " لم يتم تحديد تكلفة لها بعد " . " ( ليست جاهزة بعد )";
            }
            if (in_array($state->id, $all_bill_cases_ids)) // OR : Arr::exists($all_bill_cases_ids, $state->id )
            {
                return "حالة المريض " . $patient_name . " بتاريخ " . $formatted_state_date .
                    " موجودة بفاتورة سابقة . الرجاء إدخال تاريخ البداية بعد هذا التاريخ ";
            }
        }
        // dd($states_between_dates);

        /*
         Without transaction : 👇👇

        $bill = Bill::create([

            'dentist_id' => $request->dentist_id,
            'lab_manager_id' => $lab_id,

            'creatorable_id' => $user->id, //  Lab-Manager or Accountant
            'creatorable_type' => $userType,

            'bill_number' => "-",
            'total_cost' => null,

            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
        ]);
        $total_cost = 0;
        foreach ($states_between_dates as $state) {
            $bill_cases = BillCase::create([

                'bill_id' => $bill->id,
                'medical_case_id' => $state->id,
                'case_cost' => $state->cost
            ]);
            $total_cost += $state->cost;
        }

        $bill_number = (string)("00" . ($lab_id + 2) . "-0" . ($request->dentist_id + 1) . "-0" . $bill->id);

        $bill->update([
            'bill_number' => $bill_number,
            'total_cost' => $total_cost // 😁😀😁😀😁
        ]);

        $bill->save();

        $previous_client_account_value =
            AccountRecord::where('dentist_id', $request->dentist_id)
            ->where('lab_manager_id', $lab_id)
            ->orderBy('created_at', 'desc')->first()->current_account;

        $account = AccountRecord::create([

            'dentist_id' => $request->dentist_id,
            'lab_manager_id' => $lab_id,
            'bill_id' => $bill->id,

            'creatorable_id' => $user->id, //  Lab-Manager or Accountant
            'creatorable_type' => $userType,

            // 'note'=>, no notes here needed until now

            'type' => "سحب من الرصيد (فاتورة)",
            'signed_value' => -$bill->total_cost,
            'current_account' => $previous_client_account_value - $bill->total_cost

        ]);

         */

        /*With Transaction : 👇👇*/
        $transaction = DB::transaction(function () use ($request, $lab_id, $user, $userType, $states_between_dates) {
            $bill = Bill::create([
                'dentist_id' => $request->dentist_id,
                'lab_manager_id' => $lab_id,
                'creatorable_id' => $user->id,
                'creatorable_type' => $userType,
                'bill_number' => "-",
                'total_cost' => null,
                'date_from' => $request->date_from,
                'date_to' => $request->date_to,
            ]);

            $total_cost = 0;

            foreach ($states_between_dates as $state) {
                BillCase::create([
                    'bill_id' => $bill->id,
                    'medical_case_id' => $state->id,
                    'case_cost' => $state->cost
                ]);

                $total_cost += $state->cost;
            }

            $bill_number = "00" . ($lab_id + 2) . "-0" . ($request->dentist_id + 1) . "-0" . $bill->id;

            $bill->update([
                'bill_number' => $bill_number,
                'total_cost' => $total_cost,
            ]);

            $previous_client_account_value = AccountRecord::where('dentist_id', $request->dentist_id)
                ->where('lab_manager_id', $lab_id)
                ->orderBy('created_at', 'desc')
                ->first()
                ?->current_account ?? 0; // fallback to 0 if null

            AccountRecord::create([
                'dentist_id' => $request->dentist_id,
                'lab_manager_id' => $lab_id,
                'bill_id' => $bill->id,
                'creatorable_id' => $user->id,
                'creatorable_type' => $userType,
                'type' => "سحب من الرصيد (فاتورة)",
                'signed_value' => -$total_cost,
                'current_account' => $previous_client_account_value - $total_cost
            ]);
            return true; //return true if transaction done , i.e. $transaction =true;
        });
        if ($transaction) {
            return "done";
        }
        return "حدث خطأ ما";
    }

    ///////////////////////////////////////////////////////
    public function getAll()
    {
        return Bill::all();
    }

    public function getById($id)
    {
        return Bill::findOrFail($id);
    }

    public function getPaginate($perPage = 10)
    {
        return Bill::paginate($perPage);
    }

    public function create(array $data)
    {
        return Bill::create($data);
    }

    public function update($id, array $data)
    {
        $item = Bill::findOrFail($id);
        $item->update($data);
        return $item;
    }

    public function delete($id)
    {
        return Bill::destroy($id);
    }

    //////////////////////////////////////////////////////////////////////////

    public function search_filter_bills(Request $request)
    {
        $user = Auth::user();
        $userType = $user->getMorphClass();

        $lab_id = $userType == 'labManager'
            ? Auth::id() // labManager
            : Auth::user()->labManager->id; // accountant

        $dentists_ids_for_lab = Bill::where('lab_manager_id', $lab_id)
            ->pluck('dentist_id');

        $dentists_for_lab = Dentist::whereIntegerInRaw("id", $dentists_ids_for_lab)
            ->get(["id", "first_name", "last_name"]);

        $matched_dentist = $dentists_for_lab->first(function ($dentist) use ($request) {
            return ($dentist->first_name . ' ' . $dentist->last_name) === $request->dentist_full_name;
        });

        $bills = collect();

        if ($matched_dentist) {
            $bills = Bill::where('lab_manager_id', $lab_id)
                ->where('dentist_id', $matched_dentist->id)
                ->get();
        }

        $query = Bill::query();
        // $clients_query = User::query();

        if ($request->has('dentist_name')) {

            $dentists = Dentist::all();
            foreach ($dentists as $user) {
                $dentists_query->where('first_name', 'like', '%' . $request->input('dentist_name') . '%')
                    ->orWhere('last_name', 'like', '%' . $request->input('dentist_name') . '%');
            }
        }
        if ($request->has('bill_number')) {
            $query->where('bill_number', 'like', '%' . $request->input('bill_number') . '%');
        }
        if ($request->has('created_at')) {
            $query->whereDate('created_at', $request->input('created_at'));
        }

        $result = $query->get();
    }
}
