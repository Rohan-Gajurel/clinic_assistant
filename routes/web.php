<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\FollowupController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\LabCategoryController;
use App\Http\Controllers\LabController;
use App\Http\Controllers\LabGroupController;
use App\Http\Controllers\LabMethodController;
use App\Http\Controllers\LabOrderController;
use App\Http\Controllers\LabSampleController;
use App\Http\Controllers\LabTestController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VisitController;
use Illuminate\Support\Facades\Route;
use Termwind\Components\Raw;

Route::get('/', function () {
    return view('frontend.home');
})->name('home');



// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');



Route::prefix('appointments')->controller(AppointmentController::class)->group(function(){
    Route::get('/reschedule/{id}', 'reschedule')->name('appointments.reschedule');
    Route::put('/reschedule/{id}', 'updateReschedule')->name('appointments.storeReschedule');
    Route::get('/appointment', 'appointmentForm')->name('appointment');
    Route::get('/doctors-appointments/{id}', 'doctorsAppointments')->name('doctors.appointments');
});

Route::get('/doctor', [DoctorController::class, 'frontendDoctors'])->name('frontend.doctors');

Route::get('/followup', [FollowupController::class, 'frontendFollowups'])->name('frontend.followups');

Route::prefix('feedback')->controller(FeedbackController::class)->group(function(){
    Route::get('/create', 'create')->name('feedback.create');
    Route::post('/', 'store')->name('feedback.store');
});


// Backend Routes
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard/events', [DashboardController::class, 'events'])->name('dashboard.events');
Route::get('/calendar', [DashboardController::class, 'calender'])->name('calendar');

Route::prefix('roles')->controller(RoleController::class)->group(function() {
    Route::get('/', 'index')->name('roles.index');
    Route::get('/create', 'create')->name('roles.create');
    Route::post('/', 'store')->name('roles.store');
    Route::get('/{role}', 'edit')->name('roles.edit');
    Route::patch('/{role}', 'update')->name('roles.update');
    Route::delete('/{role}', 'destroy')->name('roles.destroy');
});

Route::prefix('permission')->controller(PermissionController::class)->group(function(){
    Route::get('/','indexPermission')->name('permissions.index');
    Route::get('/create', 'createPermission')->name('permissions.create');
    Route::post('/', 'addPermissions')->name('permissions.store');
    Route::get('/{id}','editPermissions')->name('permissions.edit');
    Route::patch('/{id}','updatePermissions')->name('permissions.update');
    Route::delete('/{id}','destroyPermissions')->name('permissions.destroy');
});

Route::prefix('users')->controller(UserController::class)->group(function(){
    Route::get('/', 'index')->name('users.users');
    Route::get('/{id}','edit')->name('users.edit');
    Route::put('/{id}','update')->name('users.update');
    Route::delete('/{id}','destroy')->name('users.destroy');
});

Route::prefix('doctors')->controller(DoctorController::class)->group(function(){
    Route::get('/', 'index')->name('doctors.doctors');
    Route::get('/create', 'create')->name('doctors.create');
    Route::post('/', 'store')->name('doctors.store');
    Route::get('/{id}/edit', 'edit')->name('doctors.edit');
    Route::put('/{id}', 'update')->name('doctors.update');
    Route::delete('/{id}', 'destroy')->name('doctors.destroy');
});

Route::prefix('schedules')->controller(ScheduleController::class)->group(function(){
    Route::get('/', 'index')->name('schedules.index');
    Route::get('/create', 'create')->name('schedules.create');
    Route::post('/', 'store')->name('schedules.store');
    Route::get('/{id}/edit', 'edit')->name('schedules.edit');
    Route::put('/{id}', 'update')->name('schedules.update');
    Route::delete('/{id}', 'destroy')->name('schedules.destroy');
});

Route::prefix('patients')->controller(PatientController::class)->group(function(){
    Route::get('/', 'index')->name('patients.index');
    Route::get('/create', 'create')->name('patients.create');
    Route::post('/', 'store')->name('patients.store');
    Route::get('/{id}/edit', 'edit')->name('patients.edit');
    Route::get('/{id}/visit', 'visitDetails')->name('patients.visitDetails');
    Route::put('/{id}', 'update')->name('patients.update');
    Route::delete('/{id}', 'destroy')->name('patients.destroy');
});
Route::get('/doctor-slots', [AppointmentController::class, 'getDoctorSlots'])->name('doctor.slots');

Route::prefix('appointments')->controller(AppointmentController::class)->group(function(){
    Route::get('/', 'index')->name('appointments.index');
    Route::get('/create/{patient_id}', 'create')->name('appointments.create');
    Route::post('/', 'store')->name('appointments.store');
    Route::get('/{id}/edit', 'edit')->name('appointments.edit');
    Route::put('/{id}', 'update')->name('appointments.update');
    Route::delete('/{id}', 'destroy')->name('appointments.destroy');
    Route::get('/frontend-appointments', 'frontendAppointments')->name('frontend.appointments');
    Route::get('/cancel/{id}', 'cancel')->name('appointments.cancel');
    Route::put('/cancel/{id}', 'cancelAppointment')->name('appointments.cancel');
});

Route::prefix('followups')->controller(FollowupController::class)->group(function(){
    Route::get('/', 'index')->name('followups.index');
    Route::get('/create', 'create')->name('followups.create');
    Route::post('/', 'store')->name('followups.store');
    Route::patch('/{id}', 'update')->name('followups.update');
    Route::delete('/{id}', 'destroy')->name('followups.destroy');
});

Route::prefix('feedback')->controller(FeedbackController::class)->group(function(){
    Route::get('/', 'index')->name('feedback.index');
    Route::get('/create', 'create')->name('feedback.create');
    Route::post('/', 'store')->name('feedback.store');
});

Route::prefix('reminders')->controller(ReminderController::class)->group(function(){
    Route::get('/', 'index')->name('reminders.index');
    Route::get('/create', 'create')->name('reminders.create');
    Route::post('/', 'store')->name('reminders.store');
    Route::patch('/{id}', 'update')->name('reminders.update');
    Route::delete('/{id}', 'destroy')->name('reminders.destroy');
});

Route::prefix('departments')->controller(DepartmentController::class)->group(function(){
    Route::get('/', 'index')->name('departments.index');
    Route::get('/create', 'create')->name('departments.create');
    Route::post('/', 'store')->name('departments.store');
    Route::get('/{id}/edit', 'edit')->name('departments.edit');
    Route::put('/{id}', 'update')->name('departments.update');
    Route::delete('/{id}', 'destroy')->name('departments.destroy');
});

Route::prefix('visit')->controller(VisitController::class)->group(function(){
    Route::post('/store-examination', 'storeExamination')->name('visit.storeExamination');
    Route::post('/store-vitals', 'upsertVitals')->name('visit.storeVitals');
    Route::post('/store-disease-history', 'storeDiseaseHistory')->name('visit.storeDiseaseHistory');
    Route::post('/store-lab-order', 'storeLabOrders')->name('visit.storeLabOrders');
});


// Lab Routes
Route::prefix('lab-category')->controller(LabCategoryController::class)->group(function(){
    Route::get('/categories', 'index')->name('lab-category.index');
    Route::get('/categories/create', 'create')->name('lab-category.create');
    Route::post('/categories', 'store')->name('lab-category.store');
    Route::get('/categories/{id}/edit', 'edit')->name('lab-category.edit');
    Route::put('/categories/{id}', 'update')->name('lab-category.update');
    Route::delete('/categories/{id}', 'destroy')->name('lab-category.destroy');
});

Route::prefix('lab-samples')->controller(LabSampleController::class)->group(function(){
    Route::get('/', 'index')->name('lab-sample.index');
    Route::get('/create', 'create')->name('lab-sample.create');
    Route::post('/', 'store')->name('lab-sample.store');
    Route::get('/{id}', 'show')->name('lab-sample.show');
    Route::get('/{id}/edit', 'edit')->name('lab-sample.edit');
    Route::put('/{id}', 'update')->name('lab-sample.update');
    Route::delete('/{id}', 'destroy')->name('lab-sample.destroy');
});

Route::prefix('lab-methods')->controller(LabMethodController::class)->group(function(){
    Route::get('/', 'index')->name('lab-method.index');
    Route::get('/create', 'create')->name('lab-method.create');
    Route::post('/', 'store')->name('lab-method.store');
    Route::get('/{id}', 'show')->name('lab-method.show');
    Route::get('/{id}/edit', 'edit')->name('lab-method.edit');
    Route::put('/{id}', 'update')->name('lab-method.update');
    Route::delete('/{id}', 'destroy')->name('lab-method.destroy');
});

Route::prefix('lab-tests')->controller(LabTestController::class)->group(function(){
    Route::get('/', 'index')->name('lab-test.index');
    Route::get('/create', 'create')->name('lab-test.create');
    Route::post('/', 'store')->name('lab-test.store');
    Route::get('/{id}', 'show')->name('lab-test.show');
    Route::get('/{id}/edit', 'edit')->name('lab-test.edit');
    Route::put('/{id}', 'update')->name('lab-test.update');
    Route::delete('/{id}', 'destroy')->name('lab-test.destroy');
});

Route::prefix('lab-group')->controller(LabGroupController::class)->group(function(){
    Route::get('/', 'index')->name('lab-group.index');
    Route::get('/create', 'create')->name('lab-group.create');
    Route::post('/', 'store')->name('lab-group.store');
    Route::get('/{id}', 'show')->name('lab-group.show');
    Route::get('/{id}/edit', 'edit')->name('lab-group.edit');
    Route::put('/{id}', 'update')->name('lab-group.update');
    Route::delete('/{id}', 'destroy')->name('lab-group.destroy');
});

Route::prefix('bills')->controller(BillController::class)->group(function(){
    Route::get('/', 'index')->name('bills.index');
    Route::get('/create', 'create')->name('bills.create');
    Route::get('/patient-search', 'patientSearch')->name('bills.patientSearch');
    Route::get('/service-search', 'serviceSearch')->name('bills.serviceSearch');
    Route::post('/', 'store')->name('bills.store');
    Route::get('/{id}', 'show')->name('bills.show');
    Route::get('/{id}/edit', 'edit')->name('bills.edit');
    Route::put('/{id}', 'update')->name('bills.update');
    Route::delete('/{id}', 'destroy')->name('bills.destroy');
});

Route::prefix('lab-orders')->controller(LabOrderController::class)->group(function(){
    Route::get('/{id}', 'show')->name('lab-orders.show');
    
});

// Lab Module Routes
Route::prefix('lab')->controller(LabController::class)->group(function(){
    Route::get('/sample-collection', 'sampleCollection')->name('lab.sample-collection');
    Route::post('/collect-sample/{id}', 'collectSample')->name('lab.collect-sample');
    Route::get('/result-entries', 'resultEntries')->name('lab.result-entries');
    Route::post('/enter-results/{id}', 'enterResults')->name('lab.enter-results');
    Route::get('/result-dispatch', 'resultDispatch')->name('lab.result-dispatch');
    Route::post('/dispatch-result/{id}', 'dispatchResult')->name('lab.dispatch-result');
    Route::get('/print-report/{id}', 'printReport')->name('lab.print-report');
});

require __DIR__.'/settings.php';
