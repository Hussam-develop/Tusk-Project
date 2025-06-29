<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LabClientsService;

class LabClientsController extends Controller
{
    public function __construct(protected LabClientsService $labClientsService)
    {
        $this->labClientsService = $labClientsService;
    }
    public function show_lab_clients()
    {
        $data = $this->labClientsService->show_lab_clients();
        return $data;
    }
}
