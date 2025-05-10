<?php

namespace App\Services;

use App\Repositories\AdminRepository;

class AdminService
{
    protected $adminRepository;

    public function __construct(AdminRepository $adminRepository)
    {
        $this->adminRepository = $adminRepository;
    }

    public function getLabs($perPage = 10)
    {
        $labs = $this->adminRepository->getActiveLabs($perPage);
        return $labs;
    }
    public function getClinics($perPage = 10)
    {
        $clinics = $this->adminRepository->getActiveClinics($perPage);
        return $clinics;
    }
    public function filterLabs($labName = null, $registerDate = null, $perPage = 10)
    {
        return $this->adminRepository->filterLabs($labName, $registerDate, $perPage);
    }

    public function filterclinics($clinic_name = null, $register_date = null, $perPage = 10)
    {
        return $this->adminRepository->filterclinics($clinic_name, $register_date, $perPage);
    }
    public function fetchLabsWithNullSubscription($perPage = 10)
    {
        return $this->adminRepository->getLabsWithNullSubscription($perPage);
    }
    public function getClinicsWithNullSubscription($perPage = 10)
    {
        return $this->adminRepository->getClinicsWithNullSubscription($perPage);
    }
    public function getLabsWithRegisterAcceptedZero($perPage = 10)
    {
        return $this->adminRepository->getLabsWithRegisterAcceptedZero($perPage);
    }

    public function getClinicsWithRegisterAcceptedZero($perPage = 10)
    {
        return $this->adminRepository->getClinicsWithRegisterAcceptedZero($perPage);
    }
    public function renewSubscription($labId, $months, $subscriptionValue)
    {
        return $this->adminRepository->renewSubscription($labId, $months, $subscriptionValue);
    }
    public function renewClinicSubscription($clinicId, $months, $subscriptionValue)
    {
        return $this->adminRepository->renewClinicSubscription($clinicId, $months, $subscriptionValue);
    }
    public function updateRegisterAccepted($id)
    {
        return $this->adminRepository->updateRegisterAccepted($id);
    }
    public function updateRegisterAcceptedclinic($id)
    {
        return $this->adminRepository->updateRegisterAcceptedclinic($id);
    }
}
