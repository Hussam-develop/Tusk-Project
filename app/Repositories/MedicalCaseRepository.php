<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Models\File;
use App\Models\Dentist;
use App\Models\Patient;
use App\Models\Treatment;
use App\Models\LabManager;
use App\Models\MedicalCase;
use App\Models\AccountRecord;
use Illuminate\Support\Facades\DB;
use app\Traits\handleResponseTrait;
use Illuminate\Support\Facades\Auth;
use App\Models\ManufacturedStatistic;
use App\Http\Requests\MedicalCaseRequest;
use App\Http\Requests\ChangeCaseStatusRequest;
use App\Http\Requests\StoreMedicalCaseAsManagerRequest;

class MedicalCaseRepository
{
    use handleResponseTrait;
    public function get_labs_by_labtype($lab_type)
    {
        // $labs = LabManager::where("lab_type", $lab_type)->get("lab_name");
        $labs = LabManager::where("lab_type", "like", "%{$lab_type}%")->get("lab_name");
        if ($labs->isEmpty()) {
            return $this->returnErrorMessage("لا توجد مخابر من النوع $lab_type ",  200);
        }
        return $this->returnData("labs", $labs, "المخابر من النوع $lab_type", 200);
    }
    public function show_lab_cases_as_doctor($lab_id)
    {
        $dentist_id = Auth::id();
        $lab_cases_as_doctor = MedicalCase::where("dentist_id", $dentist_id)
            ->where("lab_manager_id", $lab_id)
            ->orderBy('id', 'desc')
            // ->orderBy('expected_delivery_date', 'desc')
            ->with(['patient:id,full_name'])
            // ->get(["created_at", 'patient_id', 'expected_delivery_date']);
            ->get(['id', 'patient_id', 'expected_delivery_date', "created_at"]);
        // ->load("patient");

        return $lab_cases_as_doctor;
    }
    public function confirm_delivery($medical_case_id)
    {
        $medical_case = MedicalCase::where("id", $medical_case_id)->first();
        $medical_case->status = 4;
        $medical_case->confirm_delivery = true;
        $medical_case->save();
        // $user->update(["confirm_delivery" => true]);
        // $medical_case->save();
        return true;
    }

    ///////////////////////////////////////
    public function getAll()
    {
        return MedicalCase::all();
    }

    public function getById($id)
    {

        $medicalCase = MedicalCase::findOrFail($id);
        $medicalCaseFiles = File::where("medical_case_id", $medicalCase->id)->get();
        $dentist_details = Dentist::where("id", $medicalCase->dentist_id)
            ->first();
        $patient_details = Patient::where("id", $medicalCase->patient_id)
            ->first();

        return [
            "medical_case_details" => $medicalCase,
            "patient_full_name" => $patient_details->full_name,
            "patient_gender" => $patient_details->gender,
            "dentist_first_name" => $dentist_details->first_name,
            "dentist_last_name" => $dentist_details->last_name,
            "medical_case_files" => $medicalCaseFiles,
        ];
    }

    public function getPaginate($perPage = 10)
    {
        return MedicalCase::paginate($perPage);
    }

    public function create($data)
    {
        $dentist_id = Auth::id();
        $patient = Patient::where("id", $data['patient_id'])->first();

        $patient_birthday = Carbon::parse($patient->birthday); // First date
        $patient_date_at_case = Carbon::now()->format('Y-m-d')/*->toDateString()*/; // Second date
        $patient_case_age_accurate = $patient_birthday->diffInYears($patient_date_at_case); //result in years with digit point
        $patient_age_years = explode('.', $patient_case_age_accurate)[0]; //result just in years

        $medicalCase = MedicalCase::create([
            'dentist_id' => $dentist_id,
            'lab_manager_id' => $data['lab_manager_id'],
            'patient_id' => $data['patient_id'],

            'age' => $patient_age_years,

            'need_trial' => $data['need_trial'],
            'repeat' => $data['repeat'],
            'shade' => $data['shade'],
            'expected_delivery_date' => $data['expected_delivery_date'],
            'notes' => $data['notes'],
            'status' => 0, //pending
            'confirm_delivery' => 0,
            'cost' => 0,

            'teeth_crown' => $data['teeth_crown'],
            'teeth_pontic' => $data['teeth_pontic'],
            'teeth_implant' => $data['teeth_implant'],
            'teeth_veneer' => $data['teeth_veneer'],
            'teeth_inlay' => $data['teeth_inlay'],
            'teeth_denture' => $data['teeth_denture'],

            'bridges_crown' => $data['bridges_crown'],
            'bridges_pontic' => $data['bridges_pontic'],
            'bridges_implant' => $data['bridges_implant'],
            'bridges_veneer' => $data['bridges_veneer'],
            'bridges_inlay' => $data['bridges_inlay'],
            'bridges_denture' => $data['bridges_denture'],

        ]);
        if ($medicalCase) {
            $this->add_case_to_manufactured_statistic($medicalCase);
        }
        $treatment = Treatment::findOrFail($data['treatment_id']);
        $treatment->update([
            'medical_case_id' => $medicalCase->id,
        ]);
        $treatment->save();
        return $medicalCase;
    }

    public function update($case_id, $request)
    {
        $medicalCase = MedicalCase::findOrFail($case_id);
        $medicalCase->update([
            'lab_manager_id' => $request['lab_manager_id'],
            'patient_id' => $request['patient_id'],

            'need_trial' => $request['need_trial'],
            'repeat' => $request['repeat'],
            'shade' => $request['shade'],
            'expected_delivery_date' => $request['expected_delivery_date'],
            'notes' => $request['notes'],

            'teeth_crown' => $request['teeth_crown'],
            'teeth_pontic' => $request['teeth_pontic'],
            'teeth_implant' => $request['teeth_implant'],
            'teeth_veneer' => $request['teeth_veneer'],
            'teeth_inlay' => $request['teeth_inlay'],
            'teeth_denture' => $request['teeth_denture'],

            'bridges_crown' => $request['bridges_crown'],
            'bridges_pontic' => $request['bridges_pontic'],
            'bridges_implant' => $request['bridges_implant'],
            'bridges_veneer' => $request['bridges_veneer'],
            'bridges_inlay' => $request['bridges_inlay'],
            'bridges_denture' => $request['bridges_denture'],

        ]);
        $medicalCase->save();
        $treatment = Treatment::findOrFail($request['treatment_id']);
        $treatment->update([
            'medical_case_id' => $medicalCase->id,
        ]);
        $treatment->save();
        return $medicalCase;
    }

    public function delete($id)
    {
        return MedicalCase::destroy($id);
    }

    ///////////////////////////////////////////////////////////////////////////////
    ////////////////////   Lab Manager Methods    /////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////

    public function show_lab_cases_by_type()
    {
        $lab_id = auth()->user()->id;

        // 0: pending , 1 : accepted, 2 : in progress, 3: ready , 4:delivered

        // $medical_cases_by_type = MedicalCase::where("lab_manager_id", $lab_id)
        // ->get();

        $medical_cases_by_type_pending_0 = MedicalCase::where("lab_manager_id", $lab_id)
            ->where('status', 0)
            ->orderByDesc('created_at')
            ->with(['patient' => function ($query) {
                $query->select('id', "full_name");
            }])
            ->with(['dentist' => function ($query) {
                $query->select('id', 'first_name', 'last_name');
            }])
            ->get(['id', 'dentist_id', 'patient_id', 'created_at']);

        $medical_cases_by_type_pending_1 = MedicalCase::where("lab_manager_id", $lab_id)
            ->where('status', 1)
            ->orderByDesc('created_at')
            ->with(['patient' => function ($query) {
                $query->select('id', "full_name");
            }])
            ->with(['dentist' => function ($query) {
                $query->select('id', 'first_name', 'last_name');
            }])
            ->get(['id', 'dentist_id', 'patient_id', 'created_at']);

        $medical_cases_by_type_pending_2 = MedicalCase::where("lab_manager_id", $lab_id)
            ->where('status', 2)
            ->orderByDesc('created_at')
            ->with(['patient' => function ($query) {
                $query->select('id', "full_name");
            }])
            ->with(['dentist' => function ($query) {
                $query->select('id', 'first_name', 'last_name');
            }])
            ->get(['id', 'dentist_id', 'patient_id', 'created_at']);

        return [
            "pending_cases_0" => $medical_cases_by_type_pending_0,
            "accepted_cases_1" => $medical_cases_by_type_pending_1,
            "in_progress_2" => $medical_cases_by_type_pending_2,
        ];
    }
    public function dentist_cases_by_created_date_descending($dentist_id)
    {
        $lab_id = Auth::id();
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

        $dentist_cases = MedicalCase::where([
            'dentist_id' => $dentist_id,
            'lab_manager_id' => $lab_id
        ])
            ->orderByDesc('created_at')
            ->with(['patient' => function ($query) {
                $query->select('id', "full_name");
            }])
            ->get(['id', 'dentist_id', 'patient_id', 'expected_delivery_date', 'status', 'created_at']);

        return [
            "dentist" => $dentist,
            "dentist_current_balance" => $dentist_current_balance,
            "dentist_cases" => $dentist_cases,
        ];
    }
    public function change_status(ChangeCaseStatusRequest $request)
    {
        $case_id = $request->case_id;
        $case = MedicalCase::find($case_id);

        if ($case) {

            $old_status = $case->status;

            if ($old_status == 4 || $old_status == 3) //"delivered"
            {
                return "الحالة هي 'جاهزة' ولايمكن تعديلها بعد الآن";
            }

            if ($old_status == 2 /*in_progress*/) {


                $case->cost = $request->cost;
                $case->save();
            }
            $case->status = (int) $old_status + 1;
            $case->save();

            return "تم تغيير الحالة بنجاح";
        }
        return " ! حدث خطأ أثناء تغيير الحالة ";
    }
    public function add_medical_case_to_local_client(StoreMedicalCaseAsManagerRequest $request)
    {
        $lab_manager_id = Auth::id();

        $patient = Patient::firstOrCreate(
            ['phone' => $request->patient_phone], // Search condition

            [ // Data to use if creating a new patient
                'dentist_id' => $request->dentist_id,
                'full_name' => $request->patient_full_name,
                'address' => "-",
                'birthday' => $request->patient_birthdate,
                'current_balance' => null,
                'is_smoker' => $request->is_smoker,
                'gender' => $request->patient_gender,
                'medicine_name' => null,
                'illness_name' => null,
            ]
        );
        // calculate patient age
        $patient_birthday = Carbon::parse($patient->birthday); // First date
        $patient_date_at_case = Carbon::now()->format('Y-m-d')/*->toDateString()*/; // Second date
        $patient_case_age_accurate = $patient_birthday->diffInYears($patient_date_at_case); //result in years with digit point
        $patient_age_years = explode('.', $patient_case_age_accurate)[0]; //result just in years

        $medicalCase = MedicalCase::create([
            'dentist_id' => $request->dentist_id,
            'lab_manager_id' => $lab_manager_id,
            'patient_id' => $patient->id,

            'age' => $patient_age_years,

            'need_trial' => $request->need_trial,
            'repeat' => $request->repeat,
            'shade' => $request->shade,
            'expected_delivery_date' => $request->expected_delivery_date,
            'notes' => $request->notes,
            'status' => 0, //pending
            'confirm_delivery' => 0,
            'cost' => 0,

            'teeth_crown' => $request->teeth_crown,
            'teeth_pontic' => $request->teeth_pontic,
            'teeth_implant' => $request->teeth_implant,
            'teeth_veneer' => $request->teeth_veneer,
            'teeth_inlay' => $request->teeth_inlay,
            'teeth_denture' => $request->teeth_denture,

            'bridges_crown' => $request->bridges_crown,
            'bridges_pontic' => $request->bridges_pontic,
            'bridges_implant' => $request->bridges_implant,
            'bridges_veneer' => $request->bridges_veneer,
            'bridges_inlay' => $request->bridges_inlay,
            'bridges_denture' => $request->bridges_denture,

        ]);
        if ($medicalCase) {
            $this->add_case_to_manufactured_statistic($medicalCase);
        }
        return $medicalCase;
    }

    // Statistics : monthly_number_of_manufactured_pieces

    public function add_case_to_manufactured_statistic($medicalCase)
    {
        $teeth_bridges_array = [
            'teeth_crown',
            'teeth_pontic',
            'teeth_implant',
            'teeth_veneer',
            'teeth_inlay',
            'teeth_denture',
            'bridges_crown',
            'bridges_pontic',
            'bridges_implant',
            'bridges_veneer',
            'bridges_inlay',
            'bridges_denture',
        ];
        foreach ($teeth_bridges_array as $column) {
            // dd($medicalCase->$column_type);
            if ($medicalCase->$column !== null) {
                $parts = explode(',', $medicalCase->$column);
                // Step 1: Get the last value as piece_number
                $piece_number = (int) array_pop($parts);
                // Step 2: Count the remaining parts as manufactured_quantity
                $manufactured_quantity = count($parts);

                ManufacturedStatistic::create([
                    'lab_manager_id' => $medicalCase->lab_manager_id,
                    'medical_case_id' => $medicalCase->id,
                    'piece_number' => $piece_number,
                    'manufactured_quantity' => $manufactured_quantity,
                ]);
            }
            // else {
            //     // some logic maybe
            // }
        }
    }
    public function monthly_number_of_manufactured_pieces()
    {
        $lab_id = Auth::id();
        // إنشاء مصفوفة تحتوي على جميع الأشهر من 1 إلى 12
        // $allMonths = collect(range(1, 12));

        // // استعلام للحصول على الكميات السالبة
        // $stats = ManufacturedStatistic::where('lab_manager_id', $lab_id)
        //     ->where('quantity', '<', 0) // فقط الكميات السالبة
        //     ->selectRaw("MONTH(created_at) as month, SUM(quantity) as total") // جمع الكميات السالبة
        //     ->groupBy('month')
        //     ->get()
        //     ->keyBy('month'); // لتحويل النتائج إلى مصفوفة مفهرسة بالمؤشر 'month'

        // // دمج الأشهر مع النتائج لضمان وجود كل الأشهر
        // $results = $allMonths->map(function ($month) use ($stats) {
        //     // إذا لم يوجد بيانات للشهر، القيمة تكون 0
        //     return [
        //         'month' => $month,
        //         'Negative quantity' => $stats->has($month) ? abs($stats[$month]['total']) : 0 // استخدام abs لجعل القيمة موجبة
        //     ];
        // });

        // // لتحويل النتيجة إلى مصفوفة أو عرضها كيفما تريد
        // $finalResults = $results->all();
        // return $finalResults;
        $startDate = Carbon::now()->subYear()->startOfYear();
        $endDate = Carbon::now();

        $ready_cases_ids = MedicalCase::where("lab_manager_id", $lab_id)
            ->where('status', ">=", 3)
            ->pluck("id"); //ready and delivered cases only
        $results = ManufacturedStatistic::select(
            DB::raw('MONTH(created_at) as month'),    // استخراج رقم الشهر من التاريخ
            'piece_number',                              // نوع القطعة المصنعة
            DB::raw('SUM(manufactured_quantity) as total') // حساب عدد القطع
        )
            ->whereIntegerInRaw("medical_case_id", $ready_cases_ids)
            ->whereBetween("created_at", [$startDate, $endDate])
            // ->where('lab_manager_id', $lab_id) // not needed because of $ready_cases_ids
            ->groupBy('month', 'piece_number')              // التجميع حسب الشهر والنوع
            ->orderBy('month')                      // ترتيب النتائج حسب الشهر
            ->get();

        // تنظيم النتائج في مصفوفة مرتبة
        $formattedResults = [];

        foreach ($results as $result) {
            $formattedResults[$result->month][$result->piece_number] = (int) $result->total;
        }
        // dd($formattedResults);
        return $formattedResults;
    }
}
