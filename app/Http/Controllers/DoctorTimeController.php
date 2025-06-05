<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DoctorTimeService;
use App\Http\Requests\DoctorTimeRequest;

class DoctorTimeController extends Controller
{
    public function __construct(protected DoctorTimeService $doctorTimesService)
    {
        $this->doctorTimesService = $doctorTimesService;
    }
    public function getDoctorTimes()
    {
        $data = $this->doctorTimesService->getDoctorTimes();

        return $data;
    }
    public function updateDoctorTimes(DoctorTimeRequest $request)
    {
        $data = $this->doctorTimesService->updateDoctorTimes($request);

        return $data;
    }
}
