<?php

namespace Database\Seeders;

use App\Models\AccountRecord;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class accountrecord_seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    // 'dentist_id'=>$this->dentist_id,
        // 'lab_manager_id'=>$this->lab_manager_id,
        // 'bill_id'=>$this->bill_id,
        // 'creatorable_id'=>$this->creatorable_id,
        // 'creatorable_type'=>$this->creatorable_type,
        // 'note'=>$this->note,
        // 'type'=>$this->type,
        // 'signed_value'=>$this->signed_value,
        // 'current_account'=>$this->current_account,
    public function run(): void
    {
        // AccountRecord::create([
        //     'dentist_id' => 1,
        //     'lab_manager_id' => 1,
        //     'bill_id'=> 1,
        //     'creatorable_id'=> 2,
        //     'creatorable_type'=> 'lab_manager',
        //     'note'=> 'لا يوجد',
        //     'type'=> 'لا يوجد ',
        //     'signed_value'=> 2,
        //     'current_account'=> 1000000,
        // ]);
        //  AccountRecord::create([
        //     'dentist_id' => 1,
        //     'lab_manager_id' => 1,
        //     'bill_id'=> 2,
        //     'creatorable_id'=> 2,
        //     'creatorable_type'=> 'lab_manager',
        //     'note'=> 'لا يوجد',
        //     'type'=> 'لا يوجد ',
        //     'signed_value'=> 2,
        //     'current_account'=> -900000,
        // ]);
        //  AccountRecord::create([
        //     'dentist_id' => 1,
        //     'lab_manager_id' => 1,
        //     'bill_id'=>3,
        //     'creatorable_id'=> 2,
        //     'creatorable_type'=> 'lab_manager',
        //     'note'=> 'لا يوجد',
        //     'type'=> 'لا يوجد ',
        //     'signed_value'=> 2,
        //     'current_account'=> 100000,
        // ]);
        //  AccountRecord::create([
        //     'dentist_id' => 2,
        //     'lab_manager_id' => 1,
        //     'bill_id'=>3,
        //     'creatorable_id'=> 2,
        //     'creatorable_type'=> 'lab_manager',
        //     'note'=> 'لا يوجد',
        //     'type'=> 'لا يوجد ',
        //     'signed_value'=> 2,
        //     'current_account'=> 111111,
        // ]);
        // AccountRecord::create([
        //     'dentist_id' => 2,
        //     'lab_manager_id' => 1,
        //     'bill_id'=>3,
        //     'creatorable_id'=> 2,
        //     'creatorable_type'=> 'lab_manager',
        //     'note'=> 'لا يوجد',
        //     'type'=> 'لا يوجد ',
        //     'signed_value'=> 2,
        //     'current_account'=> 222222,
        // ]);
        // AccountRecord::create([
        //     'dentist_id' => 2,
        //     'lab_manager_id' => 1,
        //     'bill_id'=>3,
        //     'creatorable_id'=> 2,
        //     'creatorable_type'=> 'lab_manager',
        //     'note'=> 'لا يوجد',
        //     'type'=> 'لا يوجد ',
        //     'signed_value'=> 2,
        //     'current_account'=> 555555,
        // ]);
        AccountRecord::create([
            'dentist_id' => 2,
            'lab_manager_id' => 1,
            'bill_id'=>3,
            'creatorable_id'=> 2,
            'creatorable_type'=> 'lab_manager',
            'note'=> 'لا يوجد',
            'type'=> 'لا يوجد ',
            'signed_value'=> 2,
            'current_account'=> 666666,
        ]);
    }
}
