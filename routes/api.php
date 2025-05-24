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
        'middleware' => ['auth:admin'],
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
        Route::post('addsecretary', [DentistController::class, 'addSecretary']);
    });
});

//__________________________________________________________________end dentist routes


// راجع أن Middleware متعرف في الـ Kernel ويدعم اختيار Guard تلقائيًا
Route::middleware(['auth.guardFromToken'])->group(function () {
    Route::get('/categories', [DentistController::class, 'getcategories']);
    Route::get('/subcategories/{categoryId}', [DentistController::class, 'showSubcategories']);
    Route::post('addcategory', [DentistController::class, 'addcategory']);
    Route::get('/items/{subcategoryId}', [DentistController::class, 'showitems']);
    Route::delete('deletecategory/{id}', [DentistController::class, 'deletecategory']);
    Route::put('/updateCategory/{id}', [DentistController::class, 'updateCategory']);
    Route::delete('deleteSubcategory/{id}', [DentistController::class, 'deleteSubcategory']);
    Route::post('addsubcategory/{id}', [DentistController::class, 'addsubcategory']);
    Route::put('/updateSubCategory/{id}', [DentistController::class, 'updateSubCategory']);
    Route::post('additem/{id}', [DentistController::class, 'additem']);
    Route::delete('deleteitem/{id}', [DentistController::class, 'deleteitem']);
    Route::post('/updateitem/{id}', [DentistController::class, 'updateitem']);
    Route::post('additemhistory/{id}', [DentistController::class, 'additemhistory']);
    Route::get('/itemhistories/{itemid}', [DentistController::class, 'itemhistories']);
    Route::get('/show_labs_dentist_injoied', [DentistController::class, 'show_labs_dentist_injoied']);
    Route::get('/show_account_of_dentist_in_lab/{id}', [DentistController::class, 'show_account_of_dentist_in_lab']);
    Route::get('/show_all_labs_dentist_not_injoied', [DentistController::class, 'show_all_labs']);
    Route::get('/show_lab_not_injoied_details/{id}', [DentistController::class,'show_lab_not_injoied_details']);
    Route::get('/submit_join_request_to_lab/{id}', [DentistController::class,'submit_join_request_to_lab']);
    Route::post('/filterd_labs', [DentistController::class,'filter_not_join_labs']);









});

