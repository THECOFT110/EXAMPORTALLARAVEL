<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CollegeController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SuperAdminController;
use App\Models\College;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Root entry: Single Unified Login Board or redirect to role dashboard
Route::get('/', [AuthController::class, 'showLoginForm'])->name('home');

Route::get('/about', fn () => view('welcome'))->name('about');
Route::get('/programs', fn () => view('welcome'))->name('programs');
Route::get('/contact', fn () => view('welcome'))->name('contact');
Route::get('/faq', fn () => view('welcome'))->name('faq');

Route::get('/colleges', function () {
    $colleges = College::where('is_active', true)->orderBy('name')->get();
    return view('welcome', compact('colleges'));
})->name('colleges.public');

// Authentication routes (Guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'webLogin'])->middleware('throttle:5,1');

    Route::get('/register', fn () => view('auth.register'))->name('register');
    Route::post('/register', [AuthController::class, 'webRegister'])->middleware('throttle:5,1');

    Route::get('/forgot-password', fn () => view('auth.login'))->name('password.request');
    Route::get('/reset-password', fn () => view('auth.login'))->name('password.reset');
});

// Authenticated common routes
Route::middleware('auth')->group(function () {
    Route::get('/force-change-password', [AuthController::class, 'showForceChangePasswordForm'])->name('password.force_change');
    Route::post('/force-change-password', [AuthController::class, 'forceChangePassword'])->name('password.force_change.update');

    Route::post('/logout', [AuthController::class, 'webLogout'])->name('logout');

    Route::get('/profile', function () {
        return auth()->user()->role === 'STUDENT'
            ? redirect()->route('student.profile')
            : redirect()->route('admin.dashboard');
    })->name('profile');
});

// Student routes (StudentController)
Route::middleware(['auth', 'check.role:STUDENT'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentController::class, 'webDashboard'])->name('dashboard');
    Route::get('/profile', [StudentController::class, 'webProfile'])->name('profile');
    Route::get('/enrollments', [StudentController::class, 'webEnrollments'])->name('enrollments');
    Route::get('/enrollments/create', fn () => redirect()->route('enrollment.create'))->name('enrollments.create');
    Route::get('/enrollments/{id}', [StudentController::class, 'webEnrollmentDetails'])->name('enrollments.show');
    Route::get('/results', [StudentController::class, 'webResults'])->name('results');
    Route::get('/fees', [StudentController::class, 'webFees'])->name('fees');
});

// Enrollment web wizard routes (EnrollmentController)
Route::middleware(['auth', 'check.role:STUDENT'])->prefix('enrollment')->name('enrollment.')->group(function () {
    Route::get('/create', [EnrollmentController::class, 'webCreate'])->name('create');
    Route::post('/', [EnrollmentController::class, 'webStore'])->name('store');
    Route::post('/ocr/scan-document', [\App\Http\Controllers\OcrController::class, 'scanDocument'])->name('ocr.scan');
    Route::get('/card', [EnrollmentController::class, 'webCard'])->name('card');
    Route::get('/{id}', [EnrollmentController::class, 'webShow'])->name('details');
});

// PDF generation downloads (accessible to authenticated students, college admins, and admins)
Route::middleware(['auth'])->prefix('enrollment')->name('enrollment.')->group(function () {
    Route::get('/{feeId}/challan-pdf', [StudentController::class, 'downloadChallan'])->name('challan-pdf');
    Route::get('/{enrollmentId}/admit-card-pdf', [StudentController::class, 'downloadAdmitCard'])->name('admit-card-pdf');
    Route::get('/{enrollmentId}/result-card-pdf', [StudentController::class, 'downloadResultCard'])->name('result-card-pdf');
    Route::get('/{enrollmentId}/application-form-pdf', [StudentController::class, 'downloadApplicationForm'])->name('application-form-pdf');
    Route::get('/{enrollmentId}/enrollment-card-pdf', [StudentController::class, 'downloadEnrollmentCard'])->name('enrollment-card-pdf');
});

// College PDF reports (Admin & College Admin)
Route::middleware(['auth', 'check.role:ADMIN,SUPERADMIN,COLLEGE_ADMIN'])->group(function () {
    Route::get('/api/colleges/{collegeId}/reports/seat-list-pdf', [CollegeController::class, 'downloadSeatListPdf'])->name('api.college.reports.seat-list-pdf');
    Route::get('/api/colleges/{collegeId}/reports/complete-list-pdf', [CollegeController::class, 'downloadCompleteListPdf'])->name('api.college.reports.complete-list-pdf');
});

// Exams web routes
Route::middleware(['auth', 'check.role:STUDENT'])->prefix('exams')->name('exams.')->group(function () {
    Route::get('/form', [StudentController::class, 'webDashboard'])->name('form');
    Route::get('/fee-challan', [StudentController::class, 'webFees'])->name('fee-challan');
    Route::get('/admit-card', [StudentController::class, 'webResults'])->name('admit-card');
    Route::get('/results', [StudentController::class, 'webResults'])->name('results');
});

// Payment checkout routes (PaymentController)
Route::middleware(['auth', 'check.role:STUDENT'])->prefix('payment')->name('payment.')->group(function () {
    Route::get('/{feeId}/checkout', [PaymentController::class, 'webCheckout'])->name('checkout');
    Route::post('/{feeId}/submit', [PaymentController::class, 'webSubmitPayment'])->name('submit');
});

// Admin & College Admin routes (AdminController & SuperAdminController)
Route::middleware(['auth', 'check.role:ADMIN,SUPERADMIN,COLLEGE_ADMIN'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'webDashboard'])->name('dashboard');

    // Superadmin specific routes
    Route::get('/superadmin-dashboard', [SuperAdminController::class, 'dashboard'])
        ->name('superadmin-dashboard')
        ->middleware('check.role:SUPERADMIN');

    Route::post('/enrollment-window/toggle', [SuperAdminController::class, 'toggleEnrollmentWindow'])
        ->name('enrollment-window.toggle')
        ->middleware('check.role:SUPERADMIN,ADMIN');

    Route::get('/settings', [SuperAdminController::class, 'settings'])
        ->name('settings')
        ->middleware('check.role:SUPERADMIN');

    Route::put('/settings', [SuperAdminController::class, 'updateSettings'])
        ->name('settings.update')
        ->middleware('check.role:SUPERADMIN');

    // Enrollment approvals
    Route::post('/enrollments/{id}/approve', [AdminController::class, 'webApproveEnrollment'])->name('enrollments.approve');
    Route::post('/enrollments/{id}/reject', [AdminController::class, 'webRejectEnrollment'])->name('enrollments.reject');

    // Admin resource views
    Route::get('/enrollments', [AdminController::class, 'webEnrollments'])->name('enrollments');
    Route::get('/enrollments/index', [AdminController::class, 'webEnrollments'])->name('enrollments.index');
    Route::get('/enrollments/{id}', [AdminController::class, 'webEnrollmentDetails'])->name('enrollments.show');

    Route::get('/students', [AdminController::class, 'webStudents'])->name('students');
    Route::get('/students/index', [AdminController::class, 'webStudents'])->name('students.index');

    Route::get('/colleges', [AdminController::class, 'webColleges'])->name('colleges');
    Route::get('/colleges/index', [AdminController::class, 'webColleges'])->name('colleges.index');
    Route::get('/colleges/create', [AdminController::class, 'webCollegeCreate'])->name('colleges.create');
    Route::get('/colleges/{id}/edit', [AdminController::class, 'webCollegeEdit'])->name('colleges.edit');

    Route::get('/academic-years', [AdminController::class, 'webAcademicYears'])->name('academic-years');

    Route::get('/fees', [AdminController::class, 'webFees'])->name('fees');
    Route::get('/fees/index', [AdminController::class, 'webFees'])->name('fees.index');
    Route::get('/fees/verification', [AdminController::class, 'webFeeVerification'])->name('fees.verification');

    Route::get('/seats/index', fn () => redirect()->route('admin.dashboard'))->name('seats.index');
    Route::get('/exams/index', fn () => redirect()->route('admin.dashboard'))->name('exams.index');
    Route::get('/results/index', fn () => redirect()->route('admin.dashboard'))->name('results.index');

    Route::get('/reports', [AdminController::class, 'webReports'])->name('reports');
    Route::get('/reports/index', [AdminController::class, 'webReports'])->name('reports.index');

    Route::get('/audit-logs', [AdminController::class, 'webAuditLogs'])->name('audit-logs');

    Route::get('/users/index', [SuperAdminController::class, 'users'])
        ->name('users.index')
        ->middleware('check.role:SUPERADMIN');
});
