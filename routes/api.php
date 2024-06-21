<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\SearchController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Chat\ChatController;
use App\Http\Controllers\Chat\ChatMessageController;
use App\Http\Controllers\Control\MainController;
use App\Http\Controllers\Control\DoctorController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Medicines\MedicineController;
use App\Http\Controllers\Patient\PatientProfileController;
use App\Http\Controllers\Medicines\PatientMedicineController;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/



//auth routes
Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/logout', [AuthController::class, 'logout']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('auth/user', [AuthController::class, 'user']);
    //routes for both admin and moderator
    Route::prefix('controll')->middleware('role:admin|moderator')->group(function () {
        //routes for controll doctors
        Route::post('/assgindoctor/{user}', [DoctorController::class, 'assginDoctor']);
        Route::get('/doctors', [DoctorController::class, 'getAllDoctors']);
        Route::get('/doctor/{id}', [DoctorController::class, 'show']);
        Route::delete('/doctor/{id}', [DoctorController::class, 'deleteDoctor']);
        //get the users of the website
        Route::get('/users', [MainController::class, 'getAllUsers']);
    });


    //routes for admin only
    Route::prefix('admin')->middleware('role:admin')->group(function () {
        //admin routes for controll moderators (assign role for the user to be moderator and delete moderator)
        Route::Post('/assginmoderator/{user}', [AdminController::class, 'assginModerator']);
        Route::delete('/deleteModeartor/{id}', [AdminController::class, 'deleteModeartor']);
        Route::get('/moderator/{id}', [AdminController::class, 'showModerator']);
        Route::get('/moderators', [AdminController::class, 'getAllModerators']);
    });

    //routes for doctors, moderators and admin
    Route::prefix('main')->middleware('role:admin|moderator|doctor')->group(function () {
        // Route::resource('medicines', MedicineController::class)->except(['create', 'edit', 'index']);
        //medicine controll routes

        Route::get('medicines/all', [MedicineController::class, 'getAllMedicines']);
        Route::get('medicines/{medicine}', [MedicineController::class, 'show']);
        Route::post('/medicines', [MedicineController::class, 'store']);
        Route::put('medicines/{medicine}', [MedicineController::class, 'update']);
        Route::delete('medicines/{medicine}', [MedicineController::class, 'destroy']);
        //profiles of moderator , doctor and admin
        Route::get('/profile/{id}', [ProfileController::class, 'show']);
        Route::put('/profile/{id}', [ProfileController::class, 'update']);
    });


    //public routes
    Route::prefix('public')->group(function () {
        //get the medicines and show one medicine
        Route::get('medicines/all', [MedicineController::class, 'getAllMedicines']);
        Route::get('medicines/{medicine}', [MedicineController::class, 'show']);
        Route::apiResource('chat', ChatController::class)->only(['index', 'store', 'show']);
        Route::apiResource('chat_message', ChatMessageController::class)->only(['index', 'store']);
    });
    Route::prefix('search')->group(function () {
        Route::get('/user', [SearchController::class, 'searchUser']);
        Route::get('/medicine', [SearchController::class, 'serachMedicine']);
    });

    //patient routes
    Route::prefix('patient')->group(function () {
        //main profile
        Route::get('profile/{id}', [PatientProfileController::class, 'show']);
        Route::put('profile/{id}', [PatientProfileController::class, 'update']);
        //medcines in the profile of the patient
        Route::get('profile/medicines', [PatientMedicineController::class, 'patientMedicine']);
        Route::post('profile/medicines/assign', [PatientMedicineController::class, 'assignMedicine']);
        Route::post('profile/medicines/update/{id}', [PatientMedicineController::class, 'updateMedicine']);
        Route::delete('profile/medicines/delete/{id}', [PatientMedicineController::class, 'deleteMedicine']);
    });
});
