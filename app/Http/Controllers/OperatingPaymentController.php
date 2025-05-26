<?php

namespace App\Http\Controllers;

use App\Http\Requests\OperatingPaymentRequest;
use App\Services\OperatingPaymentService;
use Illuminate\Http\Request;

class OperatingPaymentController extends Controller
{
    public function __construct(protected OperatingPaymentService $operatingService)
    {
        $this->operatingService = $operatingService;
    }
    public function get_operating_payments()
    {
        $data = $this->operatingService->get_operating_payments();
        return $data;
    }
    public function add_operating_payments(OperatingPaymentRequest $request)
    {
        $data = $this->operatingService->add_operating_payments($request);
        return $data;
    }
}
