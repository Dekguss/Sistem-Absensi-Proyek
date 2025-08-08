<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WorkerController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Authentication Routes...
Auth::routes([
    'register' => false, // Disable registration routes
    'reset' => false,    // Disable password reset routes
    'verify' => false,   // Disable email verification routes
]);

// Protected Routes (require authentication)
Route::middleware(['auth'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('dashboard');
    Route::resource('workers', WorkerController::class);
    Route::resource('projects', ProjectController::class);
    Route::resource('attendances', AttendanceController::class);
    Route::get('/report', [AttendanceController::class, 'report'])->name('attendances.report');
    Route::get('/projects/{project}/export', [AttendanceController::class, 'export'])->name('attendances.export');
});

// Redirect authenticated users to dashboard when visiting login
Route::middleware(['guest'])->group(function () {
    Route::get('/login', 'App\Http\Controllers\Auth\LoginController@showLoginForm')->name('login');
});
