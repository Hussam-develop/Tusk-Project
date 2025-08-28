<?php


namespace App\Repositories;

use Exception;
use App\Models\Dentist;
use App\Models\LabManager;
use App\Models\Subscription;
use Tymon\JWTAuth\Facades\JWTAuth;
use app\Traits\handleResponseTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use App\Repositories\DoctorTimeRepository;
use App\Http\Controllers\Auth\MailController;

class AuthRepository implements AuthRepositoryInterface
{
    use handleResponseTrait;
    protected array $models = [
        'dentist'     => \App\Models\Dentist::class,
        'lab_manager' => \App\Models\LabManager::class,
        'inventory_employee' => \App\Models\InventoryEmployee::class,
        'accountant' => \App\Models\Accountant::class,

    ];


    public function refresh(): ?string
    {
        // Auth::shouldUse($guard);

        try {
            return JWTAuth::parseToken()->refresh();
        } catch (Exception $e) {
            return null;
        }
    }

    public function logout(): void
    {
        //Auth::shouldUse($guard);
        Auth::logout();
    }

    public function createUser(array $request_data, $guard)
    {
        // dd($request_data);
        // return $request_data;
        $modelClass = $this->models[$guard];
        try {
            $request_data['password'] = Hash::make($request_data['password']);
            $request_data['image_path'] = null;

            // $user = $modelClass::create($request_data);    Mahnoud replace that with :
            $user = $modelClass::create($request_data/* or : [
                'first_name' => $request_data["first_name"],
                'last_name' => $request_data["last_name"],
                'email' => $request_data["email"],
                'password' => $request_data["password"],
                'register_subscription_duration' => $request_data["register_subscription_duration"],
                'image_path' => $request_data["image_path"],
                'phone' => $request_data["phone"],
                'address' => $request_data["address"],
                'email_is_verified' => null,
                'email_verified_at' => null,
                'verification_code' => null,
                'register_accepted' => null,
                'register_date' => null,
                'subscription_is_valid_now' => null
            ]*/);
            // $user->subscription_is_valid_now = null;
            // $user->register_accepted = null;
            // $user->register_date = null;
            $user->save();

            //$token = JWTAuth::fromUser($user, ['guard' => $guard]);
            #------------------------------------------------------------------------
            if ($guard == "dentist") {

                // add Doctor Times Schedule
                $doctorTimeRepository = new DoctorTimeRepository();
                $doctorTimeRepository->addDoctorTimesInRegister($request_data);

                // add Doctor Image
                if (!empty($request_data['image'])) {
                    $image = $request_data['image'];

                    if ($image !== null) {

                        $filename =  $image->getClientOriginalName();

                        $file_name_existed = Dentist::where('image_path', $filename)->exists();
                        if ($file_name_existed) {
                            $filename = $this->insertRandomNumberBeforeLastDot($filename);
                        }
                        $user->image_path = $filename;
                        $user->save();

                        $image->move(public_path("project-files/profile-images"), $filename);
                    }
                }
            }
            #------------------------------------------------------------------------
            // $registerSubscription = Subscription::create([
            //     'subscriptionable_id' => $user->id, // LabManager or Dentist
            //     'subscriptionable_type' => $user->getMorphClass(),
            //     'subscription_from' => $request_data['subscription_from_date'],
            //     'subscription_to' => $request_data['subscription_to_date'],
            //     'subscription_is_valid' => 0,
            //     'subscription_value' => null,
            // ]);
            #------------------------------------------------------------------------
            $MailController = new MailController();
            $MailController->send_verification_code($request_data['guard'], $request_data['email']);

            return $user;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }


    public function getAuthenticatedUser()
    {
        $userType = Auth::user()->getMorphClass();
        $last_subscription = Subscription::where("subscriptionable_id", Auth::id())
            ->where('subscriptionable_type', $userType)->latest()->first();
        // Auth::shouldUse($guard);
        if ($userType == "dentist" || $userType == "labManager") {
            return [
                "user" => Auth::user(),
                "last_subscription" => $last_subscription
            ];
        }
        return Auth::user();
    }

    public function download_profile_image()
    {

        $file = auth()->user()->image_path;
        // $file = Dentist::where("image_path", $image_name)->first("image_path");
        if ($file) {
            $file_path = public_path('project-files/profile-images/' . $file);
            return Response::download($file_path, $file);
        } else
            return $this->returnErrorMessage("الصورة غير موجودة", 200);
    }
    public function  edit_profile_image($request)
    {
        $user = auth()->user();
        $file = $user->image_path;
        $file_path = public_path('project-files/profile-images/' . $file);
        #-------------------------------------------------------------------------------------
        $image = $request->file("image");

        if ($image !== null && $file) {
            Storage::delete($file_path);
            $filename =  $image->getClientOriginalName();

            $file_name_existed = Dentist::where('image_path', $filename)->exists();
            if ($file_name_existed) {
                $filename = $this->insertRandomNumberBeforeLastDot($filename);
            }
            $user->image_path = $filename;
            $user->save();

            $image->move(public_path("project-files/profile-images"), $filename);
            return $this->returnSuccessMessage(200, "تم تعديل الصورة بنجاح");
        }
        return $this->returnErrorMessage("حدث خطأ ما أثنا ءتعديل الصورة , يرجى المحاولة مجدداً", 422);
    }


    function insertRandomNumberBeforeLastDot($string)
    {
        // Generate a two-digit random number
        $randomNumber = rand(10, 99999);

        // Find the last occurrence of the dot
        $lastDotPos = strrpos($string, '.');

        // If there's no dot, return the original string
        if ($lastDotPos === false) {
            return $string;
        }

        // Insert the random number before the last dot
        return substr($string, 0, $lastDotPos) . $randomNumber . substr($string, $lastDotPos);
    }
}
