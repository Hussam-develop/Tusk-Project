<?php

namespace App\Http\Controllers\Auth;

use Exception;
use App\Mail\VerifyMail;
use Illuminate\Http\Request;
use App\Mail\SendWelcomeMail;
use app\Traits\handleResponseTrait;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\Mails\VerifyRequest;

class MailController extends Controller
{
    use handleResponseTrait;
    private function getModel(string $guard): string
    {
        return match ($guard) {
            'admin' => "App\\Models\\Admin",
            'lab_manager' => "App\\Models\\LabManager",
            'dentist'     => "App\\Models\\Dentist",
            'accountant' => "App\\Models\\Accountant",
            'inventory_employee' => "App\\Models\\InventoryEmployee",
            'secratary' => "App\\Models\\Secretary",
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
            return $this->returnErrorMessage("الإيميل المدخل غير مسجل في التطبيق . استخدم إيميل آخر", 404);
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

                return $this->returnSuccessMessage(200, "رمز التحقق من الإيميل صحيح");
            }
            return $this->returnErrorMessage("رمز التحقق غير مطابق. الرجاء إعادة كتابة الرمز أو طلب إعادة إرساله للإيميل", "Error", 422);
        } catch (Exception $e) {
            Log::error("Unable to send email ," . $e->getMessage());
        }
    }
}
