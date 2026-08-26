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
Route::get('/health', fn() => response()->json(['status' => 'ok', 'timestamp' => now()]));

// Authentication routes (public)
Route::prefix('auth')->group(function () {
    Route::get('/check-email', [AuthController::class, 'checkEmail']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/recover-email', [AuthController::class, 'recoverEmail']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::post('/verify-reset-token', [AuthController::class, 'verifyResetToken']);
    
    // Protected auth routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
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
        
        // Downloads (placeholders for PDF service)
        Route::get('/fees/{feeId}/challan', [StudentController::class, 'downloadChallan']);
        Route::get('/enrollments/{enrollmentId}/admit-card', [StudentController::class, 'downloadAdmitCard']);
        Route::get('/enrollments/{enrollmentId}/result-card', [StudentController::class, 'downloadResultCard']);
    });

    // Enrollment management (Student)
    Route::prefix('enrollment')->middleware('check.role:STUDENT')->group(function () {
        Route::get('/colleges', [EnrollmentController::class, 'getColleges']);
        Route::post('/', [EnrollmentController::class, 'store']);
        Route::put('/{id}', [EnrollmentController::class, 'update']);
        Route::post('/{id}/submit', [EnrollmentController::class, 'submit']);
        Route::delete('/{id}', [EnrollmentController::class, 'destroy']);
    });

    // Payment routes (Student)
    Route::prefix('payment')->group(function () {
        Route::get('/history', [PaymentController::class, 'paymentHistory'])->middleware('check.role:STUDENT');
        Route::get('/fees/{feeId}', [PaymentController::class, 'getFee']);
        Route::post('/fees/{feeId}/initiate', [PaymentController::class, 'initiatePayment']);
        Route::post('/fees/{feeId}/process', [PaymentController::class, 'processPayment'])->middleware('check.role:ADMIN,SUPERADMIN');
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
