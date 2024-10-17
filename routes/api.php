<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

// Public routes
Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login')
        ->name('login');
    Route::post('/forgot-password', 'forgotPassword')
        ->name('password.email');
    Route::post('/reset-password', 'resetPassword')
        ->name('password.reset');
    Route::post('/password/setup', 'passwordSetup')
        ->name('password.setup');
});


// adding User routes
require base_path('routes/api/users.php');

// adding Teacher routes
require base_path('routes/api/teachers.php');

// adding Student routes
require base_path('routes/api/students.php');

// adding School routes 
require base_path('routes/api/schools.php');

// adding Program routes
require base_path('routes/api/programs.php');

// adding Project routes
require base_path('routes/api/projects.php');


