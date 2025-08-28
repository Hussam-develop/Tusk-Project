<?php

namespace App\Http\Controllers\Auth;

use Exception;
use App\Mail\VerifyMail;
use Illuminate\Http\Request;
use App\Mail\SendWelcomeMail;
use app\Traits\HandleResponseTrait;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\Mails\StageRequest;
use App\Http\Requests\Mails\VerifyRequest;
use App\Http\Requests\Mails\ForgetPasswordRequest;

class MailController extends Controller
{
    use HandleResponseTrait;
    private function getModel(string $guard): string
    {
        return match ($guard) {
            'admin' => "App\\Models\\Admin",
            'lab_manager' => "App\\Models\\LabManager",
            'dentist'     => "App\\Models\\Dentist",
            'accountant' => "App\\Models\\Accountant",
            'inventory_employee' => "App\\Models\\InventoryEmployee",
            'secretary' => "App\\Models\\Secretary",
        };
    }
    public function send_welcome_mail($email, $modelName)
    {
        try {

            $modelPath = "App\\Models\\$modelName";
            $user = $modelPath::where('email', $email)->first();

            $response = Mail::to($email)->send(new SendWelcomeMail($user->first_name, $user->last_name, $modelName));
        } catch (Exception $e) {
            Log::error("Unable to send email ," . $e->getMessage());
        }
    }
    public function send_verification_code($guard, $email)
    {
        $modelPath = $this->getModel($guard);
        $user = $modelPath::where('email', $email)->first();

        $emails = $modelPath::pluck('email');

        if (!$emails->contains($email)) {
            return $this->returnErrorMessage("الإيميل المدخل غير مسجل في التطبيق . استخدم إيميل آخر", 200);
        }
        try {
            $verification_code = mt_rand(100000, 999999); //hash this in production , not testing
            $user->update([
                'verification_code' => $verification_code
            ]);
            $user->save();
            $response = Mail::to($email)->send(new VerifyMail($user->first_name, $user->last_name, $email, $verification_code, $guard)); //VerifyMail($user->email, $verification_code))
        } catch (Exception $e) {
            Log::error("حدث خطأ , لم يتم إرسال رمز التحقق للإيميل . حاول مجدداً " . $e->getMessage());
            return $this->returnErrorMessage("حدث خطأ , لم يتم إرسال رمز التحقق للإيميل . حاول مجدداً", 502);
        }
    }
    public function check_verification_code(VerifyRequest $request)
    {
        $modelPath = $this->getModel($request->guard);
        $user = $modelPath::where('email', $request->email)->first();
        try {
            if ($request->verification_code == $user->verification_code) {
                $user->email_is_verified = true;
                $user->email_verified_at = now();
                $user->save();
                return $this->returnSuccessMessage(200, "رمز التحقق من الإيميل صحيح .");
            }
            return $this->returnErrorMessage("رمز التحقق غير مطابق. الرجاء إعادة كتابة الرمز أو طلب إعادة إرساله للإيميل", "Error", 422);
        } catch (Exception $e) {
            Log::error("Unable to send email ," . $e->getMessage());
        }
    }
    public function forget_password(ForgetPasswordRequest $request)
    {
        $modelPath = $this->getModel($request->guard);
        $user = $modelPath::where('email', $request->email)->first();

        try {
            $user->update([
                'password' => Hash::make($request->new_password),
            ]);
            $user->save();
            return $this->returnSuccessMessage(200, "تمّ حفظ كلمة السر الجديدة بنجاح .");
        } catch (Exception $e) {
            Log::error("Unable to send email ," . $e->getMessage());
        }
    }
    public function stageEmployee(StageRequest $request)
    {
        $modelPath = $this->getModel($request->guard);
        $user = $modelPath::where('email', $request->email)->first();
        try {

            if ($request->verification_code == $user->verification_code) {
                $user->update([
                    'email_is_verified' => 1,
                    'email_verified_at' => now(),
                    "is_staged" => true, //only for JWT CleanCode , if MVC : unComment the two lines below.
                    'password' => Hash::make($request->stage_password)
                ]);
            } else {
                return $this->returnErrorMessage(422, "رمز التحقق غير صحيح. الرجاء المحاولة مجدداً برمز آخر أو إعادة إرسال الرمز للإيميل مرة أخرى");
            }
            // $user->save();
            // $user->update(["is_staged" => true]); //in other table in mvc Project
            $user->save();
            return $this->returnData($request->guard, $user, "تمّ توثيق الحساب  بنجاح ", 200);
        } catch (Exception $e) {
            Log::error("Unable to stage_employee ," . $e->getMessage());
            return $this->returnErrorMessage(422, "رمز التحقق غير صحيح. الرجاء المحاولة مجدداً برمز آخر أو إعادة إرسال الرمز للإيميل مرة أخرى .");
        }
    }
}
