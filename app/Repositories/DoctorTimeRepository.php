<?php

namespace App\Repositories;

use App\Models\Dentist;
use App\Models\DoctorTime;
use Illuminate\Support\Facades\Auth;

class DoctorTimeRepository
{
    public function getDoctorTimes()
    {
        $doctor_id = Auth::id();
        $doctorTimes = DoctorTime::where('dentist_id', $doctor_id)
            ->whereNotNull("start_time")->get();
        return $doctorTimes;
    }
    public function addDoctorTimesInRegister($request)
    {
        if ($request->guard == "dentist") {
            $doctor_is_registered = Dentist::where("email", $request->email)->first();
            if ($doctor_is_registered->exists()) {

                $request_data = $request->all();
                $doctor_id = $doctor_is_registered->id;
                $add_doctor_days = ["السبت", "الأحد", "الاثنين", "الثلاثاء", "الأربعاء", "الخميس", "الجمعة"];

                foreach ($add_doctor_days as $day_name) {
                    DoctorTime::create([
                        "dentist_id" => $doctor_id,
                        "day" => $day_name,
                        "start_time" => null,
                        "end_time" => null,
                        "start_rest" => null,
                        "end_rest " => null,
                    ]);
                }

                // not needed condition
                // if (!empty(array_intersect_key(array_flip($add_doctor_days), $request_data))) {
                // At least one key from $request_days exists in the request
                // return "Request contains at least one of the specified days.";
                // }

                $days_in_request = array_intersect_key($request_data, array_flip($add_doctor_days));

                foreach ($days_in_request as $day => $times) {
                    $schedule[$day] = explode(',', $times); // Convert times into an array

                    $start_time = $schedule[$day][0] ?? null; // Use null if the first value doesn't exist
                    $end_time = $schedule[$day][1] ?? null;
                    $start_rest = $schedule[$day][2] ?? null;
                    $end_rest = $schedule[$day][3] ?? null;

                    $doctor_time = DoctorTime::where('dentist_id', $doctor_id)
                        ->where('day', $day)->first();

                    $doctor_time->start_time = $start_time;
                    $doctor_time->end_time = $end_time;
                    $doctor_time->start_rest = $start_rest;
                    $doctor_time->end_rest = $end_rest;
                    $doctor_time->updated_at = now();
                    $doctor_time->save();
                };
            }
        }
    }
    public function updateDoctorTimes($request)
    {

        $request_data = $request->all();
        $doctor_id = Auth::id();
        $add_doctor_days = ["السبت", "الأحد", "الاثنين", "الثلاثاء", "الأربعاء", "الخميس", "الجمعة"];

        foreach ($add_doctor_days as $day_name) {
            $doctor_time = DoctorTime::where('dentist_id', $doctor_id)
                ->where('day', $day_name)->first();

            $doctor_time->update([
                "dentist_id" => $doctor_id,
                "day" => $day_name,
                "start_time" => null,
                "end_time" => null,
                "start_rest" => null,
                "end_rest" => null,
            ]);
            $doctor_time->save();
        }

        $days_in_request = array_intersect_key($request_data, array_flip($add_doctor_days));

        foreach ($days_in_request as $day => $times) {
            $schedule[$day] = explode(',', $times); // Convert times into an array

            $start_time = $schedule[$day][0] ?? null; // Use null if the first value doesn't exist
            $end_time = $schedule[$day][1] ?? null;
            $start_rest = $schedule[$day][2] ?? null;
            $end_rest = $schedule[$day][3] ?? null;

            $doctor_time = DoctorTime::where('dentist_id', $doctor_id)
                ->where('day', $day)->first();

            $doctor_time->start_time = $start_time;
            $doctor_time->end_time = $end_time;
            $doctor_time->start_rest = $start_rest;
            $doctor_time->end_rest = $end_rest;
            $doctor_time->updated_at = now();
            $doctor_time->save();
        };
        return true;
    }
    public function getAll()
    {
        return DoctorTime::all();
    }

    public function getById($id)
    {
        return DoctorTime::findOrFail($id);
    }

    public function getPaginate($perPage = 10)
    {
        return DoctorTime::paginate($perPage);
    }

    public function create(array $data)
    {
        return DoctorTime::create($data);
    }

    public function update($id, array $data)
    {
        $item = DoctorTime::findOrFail($id);
        $item->update($data);
        return $item;
    }

    public function delete($id)
    {
        return DoctorTime::destroy($id);
    }
}
