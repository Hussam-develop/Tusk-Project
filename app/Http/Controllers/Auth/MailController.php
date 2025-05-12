<?php

namespace App\Http\Controllers\Auth;

use Exception;
use Illuminate\Http\Request;
use App\Mail\SendWelcomeMail;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
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
}
