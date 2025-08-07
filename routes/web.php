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

Route::get('/', [HomeController::class, 'index'])->name('dashboard');

Route::resource('workers', WorkerController::class);
Route::resource('projects', ProjectController::class);
Route::resource('attendances', AttendanceController::class);
Route::get('/report', [AttendanceController::class, 'report'])->name('attendances.report');
Route::get('/projects/{project}/export', [AttendanceController::class, 'export'])->name('attendances.export');

// Route::prefix('projects/{project}/attendances')->group(function () {
//     Route::get('/', [AttendanceController::class, 'index'])->name('projects.attendances.index');
//     Route::get('/create', [AttendanceController::class, 'create'])->name('projects.attendances.create');
//     Route::post('/', [AttendanceController::class, 'store'])->name('projects.attendances.store');
//     Route::get('/{attendance}/edit', [AttendanceController::class, 'edit'])->name('projects.attendances.edit');
//     Route::put('/{attendance}', [AttendanceController::class, 'update'])->name('projects.attendances.update');
//     Route::delete('/{attendance}', [AttendanceController::class, 'destroy'])->name('projects.attendances.destroy');
//     Route::get('/report', [AttendanceController::class, 'report'])->name('projects.attendances.report');
//     Route::get('/export', [AttendanceController::class, 'export'])->name('projects.attendances.export');
// });

Auth::routes();
