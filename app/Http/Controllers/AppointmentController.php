<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookAnAppointmentRequest;
use App\Models\Appointment;
use App\Services\AppointmentService;
use app\Traits\handleResponseTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    use handleResponseTrait;

    public function __construct(protected AppointmentService $service)
    {
        $this->service = $service;
    }

    public function getAvilableSlots(Request $request)
    {

        $result = $this->service->getAvailableSlots($request);
        return $result;
        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], 422);
        }

        return $this->returnData('data', $result, 'كل المواعيد المتاحة', 200);
    }


    public function bookAnAppointment(BookAnAppointmentRequest $request)
    {
        $appointment = $this->service->bookAppointment($request);
        if (!$appointment) {
            return $this->returnErrorMessage('لم يتم حجز الموعد', 403);
        }
        return $this->returnSuccessMessage(201, 'تم حجز الموعد بنجاح');
    }

    public function getBookedAppointments(Request $request)
    {

        $appointments = $this->service->getBookedAppointments($request);
        return $this->returnData('appointments', $appointments, 'كل المواعيد المحجوزة', 200);
    }





























    public function getAvilableSlots1(Request $request)
    {

        $request->validate([
            'date' => 'required|date',
            'duration' => 'required|in:15,30,45,60',
        ]);

        $date = Carbon::parse($request->date);
        $duration = (int) $request->duration;


        //start of edit

        $user = Auth::user();
        $dentist = $this->resolveDentist($user);
        if (!$dentist) {
            return response()->json([
                'message' => 'الطبيب غير موجود.'
            ], 422);
        }

        // ✅ إعداد اليوم حسب وقت دوام الطبيب
        // $startTime = $date->copy()->setTimeFromTimeString($dentist->work_from_hour);
        // $endTime = $date->copy()->setTimeFromTimeString($dentist->work_to_hour);
        // وقت بداية ونهاية دوام الطبيب
        $startTime = Carbon::createFromFormat('H:i:s', $dentist->work_from_hour);
        $endTime = Carbon::createFromFormat('H:i:s', $dentist->work_to_hour);

        // توليد جميع الفترات الزمنية الممكنة بحسب المدة المختارة

        $allSlots = $this->generateTimeSlots($startTime, $endTime, $duration);

        // المواعيد المحجوزة لهذا التاريخ
        $bookedAppointments = Appointment::where('date', $date->toDateString())
            ->where('dentist_id', $dentist->id)
            ->get(['time_from', 'time_to'])
            ->map(function ($appt) {
                return [
                    'start' => Carbon::createFromFormat('H:i:s', $appt->time_from),
                    'end'   => Carbon::createFromFormat('H:i:s', $appt->time_to),
                    //'start' => Carbon::parse($appt->time_from),
                    //'end' => Carbon::parse($appt->time_to),
                ];
            });

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

        return response()->json([
            'date' => $date->toDateString(),
            'duration' => $duration,
            'available_slots' => $availableSlots,
        ]);
    }




    /*
    public function bookAnAppointment(BookAnAppointmentRequest $request)
    {

        $user = Auth::user();
        // تحديد الطبيب المرتبط بالمستخدم

        $dentist = $this->resolveDentist($user);


        // إنشاء الموعد
        $appointment = $user->appointments()->create([
            'dentist_id' => $dentist->id,
            'patient_name' => $request->patient_name,
            'patient_phone' => $request->patient_phone,
            'date' => $request->date,
            'time_from' => $request->time_from,
            'time_to' => $request->time_to,

        ]);



        return response()->json([
            'message' => 'Appointment created successfully',
            'appointment' => $appointment,
        ]);
    }

*/
    /*
    public function getBookedAppointments(Request $request)
    {
        //يجب عرض فقط حسب الواجهة
        $date = Carbon::parse($request->date);
        $appointments = auth()->user()
            ->appointments()->where('date', $date->toDateString())
            ->latest()
            ->get();

        return response()->json([
            'appointments' => $appointments
        ]);
    }

*/

    /**
     * Resolve the authenticated dentist from user or secretary.
     */

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
