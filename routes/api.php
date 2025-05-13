<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DentistController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\MailController;



Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

route::group([
    'prefix' => 'auth',
    'middleware' => ['auth.guardFromToken']
], function () {
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
});

/// E-Mails
Route::group(
    [
        'prefix' => 'auth/mails',
    ],
    function () {
        Route::get('/send-verification-code/{guard}/{email}', [MailController::class, 'send_verification_code']);
        Route::post('/check_verification_code', [MailController::class, 'check_verification_code']);
        // not needed I think (samae check_verification_code): Route::post('/verify-mail-code-after-register', [MailController::class, 'verify_email_code']);
        Route::post('/forget-password', [MailController::class, 'forget_password']);
    }
);
/// Stage Employees
Route::post('auth/stage-employee', [MailController::class, 'stageEmployee']);


//_________________________________________________________________admin routes
route::group(
    [
        'prefix' => 'admin',
        // 'middleware' => ['auth.guardFromToken', 'auth.MultiGuard'],
        // 'middleware' => ['auth.admin'],
    ],
    function () {
        Route::get('/subcribed-labs', [AdminController::class, 'labs']);
        Route::get('/subscribed-clinics', [AdminController::class, 'clinics']);
        Route::post('/subscribed-labs/filter', [AdminController::class, 'filterLabs']);
        Route::post('/subscribed-clinics/filter', [AdminController::class, 'filterclinics']);
        Route::get('/labs/null-subscription', [AdminController::class, 'getLabsWithNullSubscription']);
        Route::get('/clinics/null-subscription', [AdminController::class, 'getClinicsWithNullSubscription']);
        Route::get('/joinorderslabs', [AdminController::class, 'getLabsWithRegisterAcceptedZero']);
        Route::get('/joinordersclinics', [AdminController::class, 'getClinicsWithRegisterAcceptedZero']);
        // Route::post('/api/renew-subscription-of-lab', [AdminController::class, 'renewLabSubscription'])->name('api.renew.subscription');
        Route::post('/renew-subscription-of-lab', [AdminController::class, 'renewLabSubscription']);

        Route::post('/renew-subscription-of-clinic', [AdminController::class, 'renewSubscription_of_clinic'])->name('api.renew.subscription.clinic');
        Route::put('/lab-manager/accept-join-order-of-lab/{id}', [AdminController::class, 'updateRegisterAccepted']);
        Route::put('/clinic/accept-join-order-of-clinic/{id}', [AdminController::class, 'updateRegisterAcceptedclinic']);
    }
);
//_________________________________________________________________end admin routes


//__________________________________________________________________dentist routes
Route::group([
    'middleware' => ['auth:dentist'],
    'prefix' => 'dentist',
    'as' => 'dentist'
], function () {
    //Secretaries Management :
    Route::group([
        'prefix' => 'secretaries',
        'as' => 'secretaries'
    ], function () {

        Route::get('/', [DentistController::class, 'getSecretaries']);
        Route::put('/update/{id}', [DentistController::class, 'updateSecretary']);
        Route::delete('delete/{id}', [DentistController::class, 'deleteSecretary']);
    });
});

//__________________________________________________________________end dentist routes
