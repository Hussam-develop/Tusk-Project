<?php


namespace App\Services;

use app\Traits\HandleResponseTrait;
use Illuminate\Support\Facades\Auth;
use App\Repositories\LabClientsRepository;

class LabClientsService
{
    use HandleResponseTrait;

    public function __construct(protected LabClientsRepository $repository)
    {
        $this->repository = $repository;
    }

    public function show_lab_clients()
    {
        $lab_clients = $this->repository->show_lab_clients();

        if (!$lab_clients->isEmpty()) {
            return $this->returnData("lab_clients", $lab_clients, "الأطباء المشتركين", 200);
        }
        return $this->returnErrorMessage("حدث خطأ أثناء عرض الأطباء المشتركين",  422);
    }
}
