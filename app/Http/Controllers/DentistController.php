<?php

namespace App\Http\Controllers;

use App\Http\Requests\addSecretary;
use App\Http\Requests\addSecretaryRequest;
use App\Http\Requests\updatesecretaryequest;
use App\Services\SecretaryService;

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
    public function addSecretary(addSecretaryRequest $request)
    {
        $data = $request->validated();
        return $this->secretaryService->addSecretary($data);
    }
}
