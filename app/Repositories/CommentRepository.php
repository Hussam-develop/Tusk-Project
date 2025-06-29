<?php

namespace App\Repositories;


use App\Models\Comment;
use App\Models\MedicalCase;
use Illuminate\Support\Facades\DB;



class CommentRepository
{
    public function add_comment($user_id, $type, $medical_case_id, $data)
    {
        $data['medical_case_id'] = (int) $medical_case_id;
        if ($type == 'dentist') {

            $data['dentist_id'] = $user_id;
            $data['lab_manager_id'] = null;
        }
        if ($type == 'labManager') {
            $data['dentist_id'] = NULL;
            $data['lab_manager_id'] = $user_id;
        }
        return Comment::create([
            'medical_case_id' => $data['medical_case_id'],
            'dentist_id' => $data['dentist_id'],
            'lab_manager_id' => $data['lab_manager_id'],

            'comment' => $data['comment']
        ]);
    }
    public function deleteComment($user_id, $type, $id)
    {


        $comment = Comment::where('id', $id)->first();
        $dentist = $comment->dentist_id;
        $labmanger = $comment->lab_manager_id;
        if ($comment && $dentist) {
            if ($comment->dentist_id == $user_id && $type == 'dentist') {


                $comment->delete();
                return true;
            }
        }
        if ($comment && $labmanger) {
            if ($comment->lab_manager_id == $user_id && $type = 'labManager') {


                $comment->delete();
                return true;
            }
        }



        return false;
    }
    public function showCommentsOfMedicalCase($id)
    {
        $comments = Comment::where('medical_case_id', $id)->orderBy('created_at')->get();

        return $comments;
        ///// في كمالة
    }
}
