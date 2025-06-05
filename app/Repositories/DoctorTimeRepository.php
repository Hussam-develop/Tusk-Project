<?php

namespace App\Repositories;

use App\Models\DoctorTime;
use Illuminate\Support\Facades\Auth;

class DoctorTimeRepository
{
    public function getDoctorTimes()
    {
        $doctor_id = Auth::id();
        $doctorTimes = DoctorTime::where('dentist_id', $doctor_id)->get();
        return $doctorTimes;
    }
    public function updateDoctorTimes($request)
    {
        $doctor_id = Auth::id();
        foreach ($request->all() as $day => $times) {
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
        }
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
