<?php

use App\Models\MedicalCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BillController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DentistController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\MailController;
use App\Http\Controllers\TreatmentController;
use App\Http\Controllers\DoctorTimeController;
use App\Http\Controllers\LabClientsController;
use App\Http\Controllers\LabManagerController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\MedicalCaseController;
use App\Http\Controllers\PatientPaymentController;
use App\Http\Controllers\OperatingPaymentController;

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
        Route::get('/subscribed-labs', [AdminController::class, 'labs']);
        Route::get('/subscribed-clinics', [AdminController::class, 'clinics']);
        Route::post('/subscribed-labs/filter', [AdminController::class, 'filterLabs']);
        Route::post('/subscribed-clinics/filter', [AdminController::class, 'filterclinics']);
        Route::get('/labs/null-subscription', [AdminController::class, 'getLabsWithNullSubscription']);
        Route::get('/clinics/null-subscription', [AdminController::class, 'getClinicsWithNullSubscription']);
        // Route::post('/api/renew-subscription-of-lab', [AdminController::class, 'renewLabSubscription'])->name('api.renew.subscription');
        Route::post('/renew-subscription', [AdminController::class, 'renewSubscription']);

        Route::get('/labs-register-requests', [AdminController::class, 'getLabsWithRegisterAcceptedZero']);
        Route::get('/clinics-register-requests', [AdminController::class, 'getClinicsWithRegisterAcceptedZero']);
        Route::put('/accept-lab-register/{id}', [AdminController::class, 'updateRegisterAccepted']);
        Route::put('/accept-clinic-register/{id}', [AdminController::class, 'updateRegisterAcceptedclinic']);
    }
);
//_________________________________________________________________end admin routes


//__________________________________________________________________dentist routes
Route::group([
    'middleware' => ['auth:dentist'/*, 'auth.guardFromToken'*/],
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

    Route::get('show-times', [DoctorTimeController::class, 'getDoctorTimes']);
    Route::post('update-times', [DoctorTimeController::class, 'updateDoctorTimes']);
    Route::get('/download-profile-image', [AuthController::class, 'download_profile_image']);
    Route::post('/edit-profile-image', [AuthController::class, 'edit_profile_image']);

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

        // Comments
        Route::post('/add-comment/{id}', [DentistController::class, 'add_comment']);
        Route::delete('/delete-comment/{id}', [DentistController::class, 'deleteComment']);
        Route::get('/show-comments/{id}', [DentistController::class, 'showCommentsOfMedicalCase']);
    });

    // Patient Payments

    Route::group([
        'prefix' => 'patients-payments',
        'as' => 'patients-payments'
    ], function () {
        Route::get("/get-all-ordered", [PatientPaymentController::class, 'get_patients_payments_ordered']);
        Route::get("/get-history/{patient_id}", [PatientPaymentController::class, 'get_patient_payments']);
        Route::post("/add", [PatientPaymentController::class, 'add_patient_payment']);
    });

    // Bills

    Route::group([
        'prefix' => 'bills',
        'as' => 'bills'
    ], function () {

        Route::get('/show-lab-bills-descending/{lab_id}', [BillController::class, 'show_lab_bills_descending']);
        Route::get('/show-bill-details-with-cases/{bill_id}', [BillController::class, 'show_bill_details_with_cases_as_dentist']);
    });

    //_____________________________________________________________________________________الاحصائيات Statistics

    Route::group([
        // 'middleware' => [],
        'prefix' => 'statistics',
        'as' => 'statistics'
    ], function () {

        Route::get('/sub_categories_statistics', [DentistController::class, 'sub_categories_statistics']);
        Route::get('/paitents_statistics', [DentistController::class, 'paitents_statistics']);
        Route::get('/treatments_statistics', [DentistController::class, 'treatments_statistics']);
        Route::get('/Operating_Payment_statistics', [DentistController::class, 'Operating_Payment_statistics']);
        Route::get('/doctor_gains_statistics', [DentistController::class, 'doctor_gains_statistics']);
    });
    //_____________________________________________________________________________________نهاية الاحصائيات End of Statistics

    //_____________________________________________________________________________________مخابر الطبيب Labs Routes For Doctor
    Route::group([
        // 'middleware' => ['auth.guardFromToken', 'auth:dentist'],
        'prefix' => 'labs',
        'as' => 'labs'
    ], function () {

        Route::get('/show_labs_dentist_injoied', [DentistController::class, 'show_labs_dentist_injoied']);
        Route::get('/Account_records_of_lab/{id}', [DentistController::class, 'Account_records_of_lab']);
        Route::get('/show_account_of_dentist_in_lab/{id}', [DentistController::class, 'show_account_of_dentist_in_lab']);
        Route::get('/show_all_labs_dentist_not_injoied', [DentistController::class, 'show_all_labs']);
        Route::get('/show_lab_not_injoied_details/{id}', [DentistController::class, 'show_lab_not_injoied_details']);
        Route::get('/submit_join_request_to_lab/{id}', [DentistController::class, 'submit_join_request_to_lab']);
        Route::post('/filtered_labs', [DentistController::class, 'filter_not_join_labs']);
    });
    //_____________________________________________________________________________________ نهاية مخابر الطبيب End of Labs Routes For Doctor


    //patients routes :
    Route::group([
        'prefix' => 'patients',
    ], function () {

        Route::get('/', [PatientController::class, 'AllPatients']);
        Route::get('/show-patient/{patientId}', [PatientController::class, 'showPatient']);
        Route::post('/add-patient', [PatientController::class, 'storePatient']);
        /// appointments routes
        Route::get('/appointments/get-avilable-slots', [AppointmentController::class, 'getAvilableSlots']);
        Route::post('/appointments/book-an-appointment', [AppointmentController::class, 'bookAnAppointment']);
        Route::get('/appointments/get-booked-appointments', [AppointmentController::class, 'getBookedAppointments']);

        Route::post('/update-patient/{id}', [PatientController::class, 'updatePatient']);
        Route::delete('/delete-patient/{id}', [PatientController::class, 'deletePatient']);

        Route::get('/download-patient-image/{file_id}', [PatientController::class, 'download_patient_image']);
        Route::post('/add-patient-images/{patient_id}', [PatientController::class, 'add_patient_image']);
    });
});

//__________________________________________________________________end dentist routes


//__________________________________________________________________المخزون inventory
Route::group([
    'middleware' => ['auth.guardFromToken', 'auth:dentist,lab_manager,inventory_employee'],
    'prefix' => 'inventory',
    'as' => 'inventory'
], function () {

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
    Route::get('/Repeated_item_histories', [DentistController::class, 'Repeated_item_histories']);
    Route::get('/Non_Repeated_item_histories', [DentistController::class, 'Non_Repeated_item_histories']);
    Route::post('/add_nonrepeated_itemhistory', [DentistController::class, 'add_nonrepeated_itemhistory']);
});
//_____________________________________________________________________________________نهاية المخزون end inventory

//__________________________________________________________________ Operating Payments المصاريف التشغيلية

Route::group([
    'middleware' => ['auth.guardFromToken', 'auth:dentist,lab_manager,accountant'],
    'prefix' => 'operating-payments',
    'as' => 'operating-payments'
], function () {
    Route::get("/get-all", [OperatingPaymentController::class, 'get_operating_payments']);
    Route::post("/add", [OperatingPaymentController::class, 'add_operating_payments']);
});

//_____________________________________________________________________________________ end of Operating Payments نهاية المصاريف التشغيلية

//_____________________________________________________________________________________ Lab Manager مدير المخبر

Route::group([
    'middleware' => ['auth.guardFromToken', 'auth:lab_manager'],
    'prefix' => 'lab-manager',
], function () {

    // Medical Cases :
    Route::group([
        'prefix' => 'medical-cases',
    ], function () {

        Route::get("/show-lab-clients", [LabClientsController::class, 'show_lab_clients']);
        Route::get("/show-lab-cases-groubed-by-case-type", [MedicalCaseController::class, 'show_lab_cases_by_type']);
        Route::get('/get-medical-case-details/{medical_case_id}', [MedicalCaseController::class, 'get_medical_case_details']);
        Route::post('/change-status', [MedicalCaseController::class, 'change_status']);
        Route::post('/add-medical-case-to-local-client', [MedicalCaseController::class, 'add_medical_case_to_local_client']);
        Route::get("/dentist-cases-by-created-date-descending/{dentist_id}", [MedicalCaseController::class, 'dentist_cases_by_created_date_descending']);


        // Comments
        Route::post('/add-comment/{id}', [DentistController::class, 'add_comment']);
        Route::delete('/delete-comment/{id}', [DentistController::class, 'deleteComment']);
        Route::get('/show-comments/{id}', [DentistController::class, 'showCommentsOfMedicalCase']);
    });





    // Lab Statistics
    Route::group([
        'prefix' => 'statistics',
    ], function () {
        // هون اكتب الراوتات تبع الاحصائيات
        Route::get("/categories_statistics", [LabManagerController::class, 'categories_statistics']);
        Route::get("/Most_profitable_doctors", [LabManagerController::class, 'Most_profitable_doctors']);
        Route::get('/LabManager_Operating_Payment_statistics', [LabManagerController::class, 'Operating_Payment_statistics']);
        //]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]احصائيات المواد
        Route::get("/items_of_user", [LabManagerController::class, 'items_of_user']);
        Route::get("/The_monthly_consumption_of_item/{itemid}", [LabManagerController::class, 'The_monthly_consumption_of_item']);

        // in two places : add case in dentist & add case in LabManager
        Route::get("/monthly-number-of-manufactured-pieces", [LabManagerController::class, 'monthly_number_of_manufactured_pieces']);
    });
});
//_____________________________________________________________________________________ end Lab Manager
