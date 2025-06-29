<?php

namespace App\Repositories;

use App\Models\Dentist;
use App\Models\DentistLabManager;
use App\Models\LabManager;
use Illuminate\Support\Facades\Auth;

class LabClientsRepository
{
    public function show_lab_clients()
    {

        $lab_id = Auth::id();

        $lab_manager = LabManager::find($lab_id);

        $lab_dentists_ids = DentistLabManager::where('lab_manager_id', $lab_id)
            ->where('request_is_accepted', 1)->pluck("dentist_id");


        $lab_dentists = Dentist::findMany($lab_dentists_ids);

        return $lab_dentists;
    }
}
