<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CollegeController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SuperAdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Public routes
Route::get('/health', fn() => response()->json(['status' => 'ok', 'version' => config('app.version', '1.0.0')]))->middleware('throttle:60,1');

// Authentication routes (public with strict rate limiting)
Route::prefix('auth')->group(function () {
    Route::get('/check-email', [AuthController::class, 'checkEmail'])->middleware('throttle:5,1');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::post('/recover-email', [AuthController::class, 'recoverEmail'])->middleware('throttle:5,1');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->middleware('throttle:5,1');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->middleware('throttle:5,1');
    Route::post('/verify-reset-token', [AuthController::class, 'verifyResetToken'])->middleware('throttle:5,1');
    
    // Protected auth routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/force-change-password', [AuthController::class, 'apiForceChangePassword']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

// Enrollment window status (public)
Route::get('/enrollment/window/status', [AdminController::class, 'enrollmentWindowStatus']);
Route::get('/enrollment/check-window', [EnrollmentController::class, 'checkWindow']);
Route::get('/enrollment/programs', [EnrollmentController::class, 'getPrograms']);

// Colleges (public)
Route::get('/colleges', [CollegeController::class, 'index']);
Route::get('/colleges/{id}', [CollegeController::class, 'show']);

// Public payment webhook callback
Route::post('/payment/webhook/{provider}', [PaymentController::class, 'handleWebhook']);

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    
    // Student routes
    Route::prefix('student')->middleware('check.role:STUDENT')->group(function () {
        Route::get('/dashboard', [StudentController::class, 'dashboard']);
        Route::get('/profile', [StudentController::class, 'profile']);
        Route::put('/profile', [StudentController::class, 'updateProfile']);
        Route::put('/change-password', [StudentController::class, 'changePassword']);
        
        Route::get('/enrollments', [StudentController::class, 'enrollments']);
        Route::get('/enrollments/{id}', [StudentController::class, 'getEnrollment']);
        Route::get('/enrollments/{enrollmentId}/results', [StudentController::class, 'getResults']);
        
        // Downloads
        Route::get('/fees/{feeId}/challan', [StudentController::class, 'downloadChallan']);
        Route::get('/fees/{feeId}/challan-pdf', [StudentController::class, 'downloadChallan']);
        Route::get('/enrollments/{enrollmentId}/admit-card', [StudentController::class, 'downloadAdmitCard']);
        Route::get('/enrollments/{enrollmentId}/admit-card-pdf', [StudentController::class, 'downloadAdmitCard']);
        Route::get('/enrollments/{enrollmentId}/result-card', [StudentController::class, 'downloadResultCard']);
        Route::get('/enrollments/{enrollmentId}/result-card-pdf', [StudentController::class, 'downloadResultCard']);
        Route::get('/enrollments/{enrollmentId}/application-form-pdf', [StudentController::class, 'downloadApplicationForm']);
        Route::get('/enrollments/{enrollmentId}/enrollment-card-pdf', [StudentController::class, 'downloadEnrollmentCard']);
    });

    // Enrollment management (Student)
    Route::prefix('enrollment')->middleware('check.role:STUDENT')->group(function () {
        Route::get('/colleges', [EnrollmentController::class, 'getColleges']);
        Route::post('/', [EnrollmentController::class, 'store']);
        Route::put('/{id}', [EnrollmentController::class, 'update']);
        Route::post('/{id}/submit', [EnrollmentController::class, 'submit']);
        Route::delete('/{id}', [EnrollmentController::class, 'destroy']);
    });

    // Google Cloud Vision OCR document scanning
    Route::post('/ocr/scan-document', [\App\Http\Controllers\OcrController::class, 'scanDocument'])->middleware('throttle:30,1');

    // Payment routes (Authenticated)
    Route::prefix('payment')->group(function () {
        Route::get('/history', [PaymentController::class, 'paymentHistory'])->middleware('check.role:STUDENT');
        Route::get('/fees/{feeId}', [PaymentController::class, 'getFee']);
        Route::post('/fees/{feeId}/initiate', [PaymentController::class, 'initiatePayment']);
    });

    // Admin routes
    Route::prefix('admin')->middleware('check.role:ADMIN,SUPERADMIN')->group(function () {
        Route::get('/dashboard/stats', [AdminController::class, 'dashboardStats']);
        
        // Enrollment management
        Route::get('/enrollments', [AdminController::class, 'enrollments']);
        Route::get('/enrollments/{id}', [AdminController::class, 'getEnrollment']);
        Route::put('/enrollments/{id}/approve', [AdminController::class, 'approveEnrollment']);
        Route::put('/enrollments/{id}/reject', [AdminController::class, 'rejectEnrollment']);
        Route::put('/enrollments/bulk-approve', [AdminController::class, 'bulkApprove']);
        Route::put('/enrollments/bulk-reject', [AdminController::class, 'bulkReject']);
        
        // Student management
        Route::get('/students', [AdminController::class, 'students']);
        
        // Audit logs
        Route::get('/audit-logs', [AdminController::class, 'auditLogs']);
        
        // Academic year management
        Route::get('/academic-years', [AdminController::class, 'academicYears']);
        Route::post('/academic-years', [AdminController::class, 'createAcademicYear']);
        
        // Enrollment window management
        Route::post('/enrollment-window/toggle', [AdminController::class, 'toggleEnrollmentWindow']);
        Route::get('/enrollment-window/status', [AdminController::class, 'enrollmentWindowStatus']);
        
        // System settings
        Route::get('/settings', [AdminController::class, 'getSettings']);
        Route::put('/settings', [AdminController::class, 'updateSettings'])->middleware('check.role:SUPERADMIN');
        
        // College management
        Route::get('/colleges', [CollegeController::class, 'index']);
        Route::post('/colleges', [CollegeController::class, 'store']);
        Route::get('/colleges/{id}', [CollegeController::class, 'show']);
        Route::put('/colleges/{id}', [CollegeController::class, 'update']);
        Route::delete('/colleges/{id}', [CollegeController::class, 'destroy']);
        Route::get('/colleges/{id}/statistics', [CollegeController::class, 'statistics']);
        
        // Payment verification
        Route::post('/fees/{feeId}/mark-paid', [PaymentController::class, 'markAsPaid']);
        Route::post('/fees/{feeId}/verify', [PaymentController::class, 'verifyPayment']);
    });

    // Superadmin specific routes (SuperAdminController)
    Route::prefix('superadmin')->middleware('check.role:SUPERADMIN')->group(function () {
        Route::get('/dashboard', [SuperAdminController::class, 'dashboard']);
        Route::get('/settings', [SuperAdminController::class, 'settings']);
        Route::put('/settings', [SuperAdminController::class, 'updateSettings']);
        Route::get('/users', [SuperAdminController::class, 'users']);
        Route::put('/users/{id}/role', [SuperAdminController::class, 'updateUserRole']);
        Route::post('/enrollment-window/toggle', [SuperAdminController::class, 'toggleEnrollmentWindow']);
    });
});

// Fallback route
Route::fallback(function () {
    return response()->json([
        'message' => 'Endpoint not found.'
    ], 404);
});
