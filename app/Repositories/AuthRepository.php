<?php


namespace App\Repositories;

use Exception;
use App\Models\Dentist;
use App\Models\LabManager;
use Tymon\JWTAuth\Facades\JWTAuth;
use app\Traits\handleResponseTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\registerRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class AuthRepository implements AuthRepositoryInterface
{
    use handleResponseTrait;
    protected array $models = [
        'dentist'     => \App\Models\Dentist::class,
        'lab_manager' => \App\Models\LabManager::class,
    ];


    public function refresh(): ?string
    {
        // Auth::shouldUse($guard);

        try {
            return JWTAuth::parseToken()->refresh();
        } catch (\Exception $e) {
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

        $modelClass = $this->models[$guard];
        try {
            $request_data['password'] = Hash::make($request_data['password']);

            $user = $modelClass::create($request_data);
            //$token = JWTAuth::fromUser($user, ['guard' => $guard]);
            #------------------------------------------------------------------------
            if ($guard == "dentist") {

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
            #------------------------------------------------------------------------

            return $user;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }


    public function getAuthenticatedUser()
    {
        // Auth::shouldUse($guard);
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
            return $this->returnErrorMessage("الصورة غير موجودة", 404);
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
