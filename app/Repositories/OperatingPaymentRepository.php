<?php

namespace App\Repositories;

use App\Models\OperatingPayment;
use Illuminate\Support\Facades\Auth;

class OperatingPaymentRepository
{
    public function getAll()
    {
        $user = auth()->user();
        $user_type = $user->getMorphClass();
        $operatingPayment =  OperatingPayment::where("creatorable_id", $user->id)
            ->where("creatorable_id", $user->id)
            ->where("creatorable_type", $user_type)
            ->orderBy('id', 'desc')
            ->get();
        return $operatingPayment;
    }

    public function getById($id)
    {
        return OperatingPayment::findOrFail($id);
    }

    public function getPaginate($perPage = 10)
    {
        return OperatingPayment::paginate($perPage);
    }

    public function create($request)
    {
        $user = auth()->user();
        $user_type = $user->getMorphClass();

        $operatingPayment = OperatingPayment::create([

            'creatorable_id' => $user->id,
            'creatorable_type' => $user_type,

            'name' => $request->name,
            'value' => $request->value,
        ]);
        if ($operatingPayment) {
            return true;
        }
        return false;
    }

    public function update($id, array $data)
    {
        $item = OperatingPayment::findOrFail($id);
        $item->update($data);
        return $item;
    }

    public function delete($id)
    {
        return OperatingPayment::destroy($id);
    }
}
