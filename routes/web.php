<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\JobApplicationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [JobApplicationController::class, 'index'])->name('applications.index');
Route::get('/applications/{id}', [JobApplicationController::class, 'show'])->name('applications.show');
Route::patch('/applications/{id}/status', [JobApplicationController::class, 'updateStatus'])->name('applications.update-status');
