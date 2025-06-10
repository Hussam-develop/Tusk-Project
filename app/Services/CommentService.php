<?php

namespace App\Services;

use App\Http\Controllers\Auth\MailController;

use app\Traits\handleResponseTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Repositories\CommentRepository;



class CommentService
{
    use handleResponseTrait;
    protected $CommentRepository;

    public function __construct(CommentRepository $CommentRepository)
    {
        $this->CommentRepository = $CommentRepository;
    }
    public function add_comment($medical_case_id, $data)
    {
        $user = auth()->user(); // المستخدم الحالي بعد تحديد Guard بواسطة Middleware
        $type = $user->getMorphClass();
        $fromaddrepo = $this->CommentRepository->add_comment($user->id, $type, $medical_case_id, $data);

        if ($fromaddrepo) {
            return $this->returnSuccessMessage(200, 'تم إضافة التعليق  بنجاح. ');
        }
    }
    public function deleteComment($id)
    {
        $user = auth()->user(); // المستخدم الحالي بعد تحديد Guard بواسطة Middleware
        $type = $user->getMorphClass();

        $fromrepo = $this->CommentRepository->deleteComment($user->id, $type, $id);
        if ($fromrepo) {
            return $this->returnSuccessMessage(200, 'تم حذف  التعليق  بنجاح. ');
        }
        return $this->returnErrorMessage('لم يتم حذف التعليق  لانه غير موجود او انك غير مخول ', 404);
    }
    public function showCommentsOfMedicalCase($id)
    {


        $fromrepo = $this->CommentRepository->showCommentsOfMedicalCase($id);
        if ($fromrepo->isEmpty()) {
            return $this->returnErrorMessage('لا يوجد تعليقات', 404);
        }
        return $this->returnData("comments", $fromrepo, "التعليقات", 200);
    }
}
//
