<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\ImportController;
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
        Route::delete('/patients/{id}', [PatientController::class, 'destroy'])->name('patients.destroy');

        // Import Routes
        Route::get('/import', [ImportController::class, 'index'])->name('import.index');
        Route::post('/import', [ImportController::class, 'store'])->name('import.store');
        Route::get('/import/template', [ImportController::class, 'downloadTemplate'])->name('import.template');

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
