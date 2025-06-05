<?php

namespace App\Repositories;

use App\Models\Appointment;
use Illuminate\Support\Collection;

class AppointmentRepository
{
    public function getAppointmentsByDateAndDentist(string $date, int $dentistId): Collection
    {
        return Appointment::where('date', $date)
            ->where('dentist_id', $dentistId)
            ->get(['time_from', 'time_to']);
    }

    public function createAppointment(array $data): Appointment
    {
        return Appointment::create($data);
    }

    public function getUserAppointmentsByDate($user, string $date): Collection
    {
        return $user->appointments()->where('date', $date)->latest()->get();
    }
}
