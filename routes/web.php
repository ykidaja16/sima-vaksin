<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\ManualInputController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\VaccineTypeController;
use Illuminate\Support\Facades\Route;

// Public Routes (Login)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes (Require Authentication)
Route::middleware(['auth'])->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Redirect root to patients list
    Route::get('/', function () {
        return redirect()->route('patients.index');
    });

    // Admin Routes (Operational)
    Route::middleware(['role:admin'])->group(function () {
        // Patient Routes
        Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');
        Route::get('/patients/export/excel', [PatientController::class, 'exportExcel'])->name('patients.export.excel');
        Route::get('/patients/export/pdf', [PatientController::class, 'exportPDF'])->name('patients.export.pdf');
Route::get('/patients/{id}', [PatientController::class, 'show'])->name('patients.show');
        Route::put('/patients/{id}/vaccine-first-date', [PatientController::class, 'updateVaccineFirstDate'])->name('patients.update-vaccine-first-date');
        Route::delete('/patients/{id}/vaccine', [PatientController::class, 'destroyVaccine'])->name('patients.destroy-vaccine');
        Route::get('/patients/{id}/edit', [PatientController::class, 'edit'])->name('patients.edit');
        Route::put('/patients/{id}', [PatientController::class, 'update'])->name('patients.update');
        Route::delete('/patients/{id}', [PatientController::class, 'destroy'])->name('patients.destroy');

        // Input Data Routes (Import Excel & Input Manual)
        Route::get('/input-data', [ImportController::class, 'index'])->name('import.index');
        Route::get('/input-data/import', [ImportController::class, 'index'])->name('import.excel');
        Route::post('/input-data/import', [ImportController::class, 'store'])->name('import.store');
        Route::get('/input-data/template', [ImportController::class, 'downloadTemplate'])->name('import.template');
        
        // Input Manual Routes
        Route::get('/input-data/manual', [ManualInputController::class, 'index'])->name('manual-input.index');
        Route::post('/input-data/manual/add', [ManualInputController::class, 'addToSession'])->name('manual-input.add');
        Route::post('/input-data/manual/save', [ManualInputController::class, 'save'])->name('manual-input.save');
        Route::post('/input-data/manual/clear', [ManualInputController::class, 'clear'])->name('manual-input.clear');
        Route::delete('/input-data/manual/remove/{id}', [ManualInputController::class, 'remove'])->name('manual-input.remove');
        Route::get('/input-data/manual/edit/{id}', [ManualInputController::class, 'editSession'])->name('manual-input.edit');
        Route::put('/input-data/manual/update/{id}', [ManualInputController::class, 'updateSession'])->name('manual-input.update');

        // Reminder Routes
        Route::get('/reminders', [ReminderController::class, 'index'])->name('reminders.index');
        Route::get('/reminders/export/excel', [ReminderController::class, 'exportExcel'])->name('reminders.export.excel');
        Route::get('/reminders/export/pdf', [ReminderController::class, 'exportPDF'])->name('reminders.export.pdf');
        Route::post('/reminders/{id}/complete', [ReminderController::class, 'complete'])->name('reminders.complete');
        Route::post('/reminders/{id}/sent', [ReminderController::class, 'markReminderSent'])->name('reminders.sent');
    });

    // IT Routes (Master Data Management)
    Route::middleware(['role:it'])->group(function () {
        // User Management
        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
        Route::put('/users/{id}', [UserManagementController::class, 'update'])->name('users.update');
        Route::delete('/users/{id}', [UserManagementController::class, 'destroy'])->name('users.destroy');
        Route::patch('/users/{id}/toggle-active', [UserManagementController::class, 'toggleActive'])->name('users.toggle-active');

        // Vaccine Type Management
        Route::get('/vaccine-types', [VaccineTypeController::class, 'index'])->name('vaccine-types.index');
        Route::post('/vaccine-types', [VaccineTypeController::class, 'store'])->name('vaccine-types.store');
        Route::put('/vaccine-types/{id}', [VaccineTypeController::class, 'update'])->name('vaccine-types.update');
        Route::delete('/vaccine-types/{id}', [VaccineTypeController::class, 'destroy'])->name('vaccine-types.destroy');
        Route::patch('/vaccine-types/{id}/toggle-active', [VaccineTypeController::class, 'toggleActive'])->name('vaccine-types.toggle-active');

        // Branch Management
        Route::get('/branches', [BranchController::class, 'index'])->name('branches.index');
        Route::post('/branches', [BranchController::class, 'store'])->name('branches.store');
        Route::put('/branches/{id}', [BranchController::class, 'update'])->name('branches.update');
        Route::delete('/branches/{id}', [BranchController::class, 'destroy'])->name('branches.destroy');
        Route::patch('/branches/{id}/toggle-active', [BranchController::class, 'toggleActive'])->name('branches.toggle-active');
    });
});
