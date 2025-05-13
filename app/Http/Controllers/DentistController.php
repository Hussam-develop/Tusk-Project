<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SecretaryService;
use App\Http\Requests\updatesecretaryequest;

class DentistController extends Controller
{
    protected $secretaryService;

    public function __construct(SecretaryService $secretaryService)
    {
        $this->secretaryService = $secretaryService;
    }

    public function getSecretaries()
    {
        return $this->secretaryService->getSecretaries();
    }
    public function updateSecretary($id, updatesecretaryequest $request)
    {
        $data = $request->validated();
        return $this->secretaryService->updateSecretary($id, $data);
    }
    public function deleteSecretary($id)
    {
        return $this->secretaryService->removeSecretary($id);
    }
}
