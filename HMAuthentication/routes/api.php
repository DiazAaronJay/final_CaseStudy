<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum')->name('logout');

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('/patients', PatientController::class)->names([
        'index' => 'patients.index',
        'show' => 'patients.show',
        'store' => 'patients.store',
        'update' => 'patients.update',
        'destroy' => 'patients.destroy',
    ]);

    Route::apiResource('/doctors', DoctorController::class)->names([
        'index' => 'doctors.index',
        'show' => 'doctors.show',
        'store' => 'doctors.store',
        'update' => 'doctors.update',
        'destroy' => 'doctors.destroy',
    ]);

    Route::apiResource('/appointments', AppointmentController::class)->names([
        'index' => 'appointments.index',
        'show' => 'appointments.show',
        'store' => 'appointments.store',
        'update' => 'appointments.update',
        'destroy' => 'appointments.destroy',
    ]);

    Route::apiResource('/records', MedicalRecordController::class)->names([
        'index' => 'records.index',
        'show' => 'records.show',
        'store' => 'records.store',
        'update' => 'records.update',
        'destroy' => 'records.destroy',
    ]);

    Route::apiResource('/profile', UserController::class)->names([
        'index' => 'profile.index',
        'update' => 'profile.update',
    ]);

    Route::get('/user', function (Request $request) {
        return $request->user();
    })->name('user');

    Route::get('/user/role', [AuthController::class, 'getUserRole'])->name('user.role');

});

