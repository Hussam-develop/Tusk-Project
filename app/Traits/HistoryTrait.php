<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait HistoryTrait
{

    protected function addToHistory($user_id, $user_type, $operation_name, $details, $date_time)
    {
        DB::table('histories')->insert([
            [
                'userable_id' => $user_id,
                'userable_type' => $user_type,
                'operation_name' => $operation_name,
                'details' => $details,
                'date_time' => $date_time
            ]
        ]);
    }
}
