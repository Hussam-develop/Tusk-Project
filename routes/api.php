<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DentistController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\MailController;
use App\Http\Controllers\MedicalCaseController;
use App\Http\Controllers\TreatmentController;
use App\Models\MedicalCase;

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

    // Treatments

    Route::group([
        'prefix' => 'treatments',
        'as' => 'treatments'
    ], function () {
        Route::get("/show-patient-treatments/{patient_id}", [TreatmentController::class, 'show_patient_treatments']);
        Route::get("/show-treatment-details/{treatment_id}", [TreatmentController::class, 'show_treatment_details']);

        Route::post("/add", [TreatmentController::class, 'add_treatment']);
        Route::put("/update/{treatment_id}", [TreatmentController::class, 'update_treatment']);

        Route::get('/download-treatment-image/{file_id}', [TreatmentController::class, 'download_treatment_image']);
        Route::post('/add-treatment-images/{treatment_id}', [TreatmentController::class, 'add_treatment_image']);
    });

    // MedicalCases

    Route::group([
        'prefix' => 'medical-cases',
        'as' => 'medical-cases'
    ], function () {
        Route::get("/get-labs-by-labtype/{lab_type}", [MedicalCaseController::class, 'get_labs_by_labtype']);
        Route::get('/show-lab-medical-cases/{lab_id}', [MedicalCaseController::class, 'show_lab_cases_as_doctor']);
        Route::get('/get-medical-case-details/{medical_case_id}', [MedicalCaseController::class, 'get_medical_case_details']);

        Route::post("/add-medical-case-to-lab", [MedicalCaseController::class, 'add_medical_case_to_lab']);
        Route::post("/update/{case_id}", [MedicalCaseController::class, 'update_case']);
        Route::post("/delete/{case_id}", [MedicalCaseController::class, 'delete_case']);

        Route::post('/request-cancellation/{medical_case_id}', [MedicalCaseController::class, 'delete_request']);
        Route::post('/confirm-delivery/{medical_case_id}', [MedicalCaseController::class, 'confirm_delivery']);

        Route::post('/add-case-images/{case_id}', [MedicalCaseController::class, 'add_case_images']);
        Route::get('/download-case-image/{case_id}', [MedicalCaseController::class, 'download_medical_case_image']);
    });
});


//__________________________________________________________________end dentist routes
