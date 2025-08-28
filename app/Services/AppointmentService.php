<?php

namespace App\Services;

use App\Models\DoctorTime;
use App\Repositories\AppointmentRepository;
use app\Traits\HandleResponseTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentService
{
    use HandleResponseTrait;
    protected $repo;

    public function __construct(AppointmentRepository $repo)
    {
        $this->repo = $repo;
    }

    public function getAvailableSlots(Request $request)
    {
        /*  $request->validate([
            'date' => 'required|date',
            'duration' => 'required|in:15,30,45,60',
        ]); */
        $date = Carbon::parse($request->date);

        $dayName = Carbon::parse($date)->locale('ar')->dayName;
        $duration = (int) $request->duration;
        $user = Auth::user();
        $dentist = $user instanceof \App\Models\Dentist ? $user : ($user->dentist ?? null);

        if (!$dentist) {
            return ['error' => 'الطبيب غير موجود.'];
        }


        $doctor_time = DoctorTime::where("dentist_id", $dentist->id)
            ->where("day", $dayName)
            ->first();
        if ($doctor_time->start_time == null) {
            return ['error' => "يوم $dayName ليس ضمن أيام دوام العيادة . يرجى تغيير التاريخ أو تعديل أوقات الدوام "];
        }
        $startTime = Carbon::createFromFormat('H:i:s', $doctor_time->start_time);
        $endTime = Carbon::createFromFormat('H:i:s', $doctor_time->end_time);
        // $startTime = $doctor_time->start_time;
        // $endTime = $doctor_time->end_time;

        // توليد جميع الفترات الزمنية الممكنة بحسب المدة المختارة
        $allSlots = $this->generateTimeSlots($startTime, $endTime, $duration);


        $appointments = $this->repo->getAppointmentsByDateAndDentist($date->toDateString(), $dentist->id);
        $bookedAppointments = $appointments->map(fn($appt) => [
            'start' => Carbon::createFromFormat('H:i:s', $appt->time_from),
            'end'   => Carbon::createFromFormat('H:i:s', $appt->time_to),
        ]);
        // dd($doctor_time->start_rest == null);
        if ($doctor_time->start_rest == null) {
            // فلترة الفترات الزمنية المتاحة (استبعاد المتداخلة)
            $availableSlots = collect($allSlots)->reject(function ($slot) use ($bookedAppointments, $date) {
                // $slotStart = Carbon::createFromFormat('H:i', $slot['start'])->setDateFrom($date);
                // $slotEnd = Carbon::createFromFormat('H:i', $slot['end'])->setDateFrom($date);
                $slotStart = Carbon::createFromFormat('H:i', $slot['start']);
                $slotEnd   = Carbon::createFromFormat('H:i', $slot['end']);
                foreach ($bookedAppointments as $appt) {
                    if ($slotStart->lt($appt['end']) && $appt['start']->lt($slotEnd)) {
                        return true; // يوجد تداخل، استبعِد هذا الـ slot
                    }
                }

                return false; // مقبول
            })->values();
        }
        if (!$doctor_time->start_rest == null) {
            // dd($doctor_time->start_rest, $doctor_time->end_rest);
            $start_rest = Carbon::createFromFormat('H:i:s', $doctor_time->start_rest);
            $end_rest = Carbon::createFromFormat('H:i:s', $doctor_time->end_rest);

            $availableSlots = collect($allSlots)->reject(function ($slot) use ($bookedAppointments, $date, $start_rest, $end_rest) {
                $slotStart = Carbon::createFromFormat('H:i', $slot['start']);
                $slotEnd   = Carbon::createFromFormat('H:i', $slot['end']);

                // Check if slot overlaps with booked appointments
                foreach ($bookedAppointments as $appt) {
                    if ($slotStart->lt($appt['end']) && $appt['start']->lt($slotEnd)) {
                        return true; // Overlaps, exclude this slot
                    }
                }

                // Check if slot falls within rest period
                if ($slotStart->lt($end_rest) && $start_rest->lt($slotEnd)) {
                    return true; // Falls within rest time, exclude this slot
                }

                return false; // Acceptable slot
            })->values();
        }

        return [
            'date' => $date->toDateString(),
            'day_name' => $dayName,
            'duration' => $duration,
            'available_slots' => $availableSlots,
        ];
    }

    public function bookAppointment($request)
    {
        $user = Auth::user();
        $dentistId = $user instanceof \App\Models\Dentist
            ? $user->id
            : ($user->dentist->id ?? null);

        $data = array_merge($request->only([
            'patient_name',
            'patient_phone',
            'date',
            'time_from',
            'time_to'
        ]), ['dentist_id' => $dentistId]);

        return $user->appointments()->create($data);
    }

    public function getBookedAppointments(Request $request)
    {
        $date = Carbon::parse($request->date);
        $user = auth()->user();

        return $this->repo->getUserAppointmentsByDate($user, $date->toDateString());
    }





    private function resolveDentist($user)
    {
        if ($user instanceof \App\Models\Dentist) {
            return $user;
        }

        if ($user instanceof \App\Models\Secretary) {
            return $user->dentist;
        }

        return null;
    }

    /**
     * Generate all time slots based on start, end, and duration.
     */

    private function generateTimeSlots(Carbon $start, Carbon $end, int $duration): array
    {
        $slots = [];
        $current = $start->copy();

        while ($current->addMinutes($duration)->lte($end)) {
            $slots[] = [
                'start' => $current->copy()->subMinutes($duration)->format('H:i'),
                'end'   => $current->format('H:i'),
            ];
        }

        return $slots;
    }
}
