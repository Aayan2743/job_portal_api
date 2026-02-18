<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\OtpAuthController;
use App\Http\Controllers\PostedJobController;
use App\Http\Controllers\RecruiterController;
use App\Http\Controllers\RecruiterDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('admin/dashboard', [DashboardController::class, 'index']);

Route::prefix('auth')->group(function () {

    // ================= AUTH =================

    Route::post('/send-otp', [OtpAuthController::class, 'sendOtp']);
    Route::post('/verify-login-otp', [OtpAuthController::class, 'verifyOtp']);

    Route::post('super-admin-login', [AuthController::class, 'super_admin_login']);
    Route::post('admin-register', [AuthController::class, 'admin_register']);
    Route::post('admin-login', [AuthController::class, 'admin_login']);
    Route::post('recruiter-login', [AuthController::class, 'recruiter_login']);
    Route::post('user-register', [AuthController::class, 'register']);
    Route::post('user-login', [AuthController::class, 'login']);

    // ================= PASSWORD / OTP =================
    Route::post('forgot-password', [AuthController::class, 'sendOtp']);
    Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);

    Route::post('organization/forgot-password', [AuthController::class, 'OrgsendOtp']);

});

// Route::prefix('admin-dashboard')->middleware(['api', 'jwt.auth'])->group(function () {
Route::prefix('admin-dashboard')->group(function () {

    Route::post('recruiter/login', [RecruiterController::class, 'login']);
    Route::post('recruiter', [RecruiterController::class, 'store']);
    Route::get('recruiter', [RecruiterController::class, 'index']);
    Route::get('recruiter/{id}', [RecruiterController::class, 'show']);
    Route::put('recruiter/{id}', [RecruiterController::class, 'update']);
    Route::delete('recruiter/{id}', [RecruiterController::class, 'destroy']);
    Route::get('recruiter-deleted', [RecruiterController::class, 'deletedRecruiters']);
    Route::post('recruiter-recovery/{id}', [RecruiterController::class, 'restore']);

    // all jobs
    Route::get('all-jobs', [PostedJobController::class, 'allJobs']);

    Route::get('/applications', [JobApplicationController::class, 'index']);
    Route::get('/applications/{id}', [JobApplicationController::class, 'show']);
    Route::get('/jobs/{id}/applicants', [PostedJobController::class, 'jobApplicants']);
});

Route::prefix('recruiter-dashboard')->middleware(['api', 'jwt.auth'])->group(function () {

    Route::post('recruiter/post-job', [PostedJobController::class, 'store']);
    Route::get('recruiter/my-jobs', [PostedJobController::class, 'myJobs']);
    Route::put('recruiter/post-job/{id}', [PostedJobController::class, 'update']);
    Route::delete('recruiter/post-job/{id}', [PostedJobController::class, 'destroy']);

    Route::get('/applications', [JobApplicationController::class, 'index']);
    Route::get('/applications/{id}', [JobApplicationController::class, 'show']);

    Route::get('/', [RecruiterDashboardController::class, 'index']);

});

Route::prefix('landing')->group(function () {

    Route::get('all-jobs', [PostedJobController::class, 'openJobs']);
    Route::post('/apply-job', [JobApplicationController::class, 'store']); // Public

});
