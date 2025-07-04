<?php

namespace App\Repositories;
use App\Models\ManufacturedStatistic;

class ManufacturedStatisticRepository
{
     public function getAll()
     {
         return ManufacturedStatistic::all();
     }

     public function getById($id)
     {
         return ManufacturedStatistic::findOrFail($id);
     }

     public function getPaginate($perPage = 10)
     {
         return ManufacturedStatistic::paginate($perPage);
     }

     public function create(array $data)
     {
         return ManufacturedStatistic::create($data);
     }

     public function update($id, array $data)
     {
         $item = ManufacturedStatistic::findOrFail($id);
         $item->update($data);
         return $item;
     }

     public function delete($id)
     {
         return ManufacturedStatistic::destroy($id);
     }
}