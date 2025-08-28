<?php

namespace App\Services;

use App\Repositories\DoctorTimeRepository;
use app\Traits\HandleResponseTrait;

class DoctorTimeService
{
    use HandleResponseTrait;
    protected $repository;

    public function __construct(DoctorTimeRepository $repository)
    {
        $this->repository = $repository;
    }


    public function getDoctorTimes()
    {
        $data = $this->repository->getDoctorTimes();
        if ($data) {
            return $this->returnData("dentist_schedule", $data,  'أوقات الدوام', 200);
        }
        return $this->returnErrorMessage(422, 'حدث خطأ أثناء جلب أوقات الدوام . الرجاء المحاولة مرة أخرى');
    }
    public function updateDoctorTimes($request)
    {
        $update_data = $this->repository->updateDoctorTimes($request);
        if ($update_data) {
            return $this->returnSuccessMessage(201, 'تم تعديل أوقات الدوام بنجاح');
        }
        return $this->returnErrorMessage(422, 'حدث خطأ . لم يتم تعديل أوقات الدوام . الرجاء المحاولة مرة أخرى');
    }
    public function getAll()
    {
        return $this->repository->getAll();
    }

    public function getById($id)
    {
        return $this->repository->getById($id);
    }

    public function getPaginate($perPage = 10)
    {
        return $this->repository->getPaginate($perPage);
    }

    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    public function update($id, array $data)
    {
        return $this->repository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }
}
