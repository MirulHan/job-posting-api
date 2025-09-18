<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobPostController;
use App\Http\Controllers\JobApplicationController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Job Post Routes
Route::post('/job-posts', [JobPostController::class, 'store']);
Route::get('/job-posts', [JobPostController::class, 'index']);
Route::get('/job-posts/{id}', [JobPostController::class, 'show']);

// Job Application Routes
Route::post('/job-applications', [JobApplicationController::class, 'store']);
Route::get('/job-applications', [JobApplicationController::class, 'index']);
Route::get('/job-applications/{id}', [JobApplicationController::class, 'show']);
