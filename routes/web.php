<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CollegeController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SuperAdminController;
use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\College;
use App\Models\Enrollment;
use App\Models\EnrollmentWindow;
use App\Models\Fee;
use App\Models\User;
use App\Services\FileUploadService;
use App\Services\PdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Root entry: Show Single Unified Login Board at first, or redirect to role dashboard if logged in
Route::get('/', function () {
    if (Auth::check()) {
        return (new AuthController)->redirectBasedOnRole(Auth::user());
    }
    return view('auth.login');
})->name('home');

Route::get('/about', function () {
    return view('welcome');
})->name('about');

Route::get('/colleges', function () {
    $colleges = College::where('is_active', true)->orderBy('name')->get();
    return view('welcome', compact('colleges'));
})->name('colleges.public');

Route::get('/programs', function () {
    return view('welcome');
})->name('programs');

Route::get('/contact', function () {
    return view('welcome');
})->name('contact');

Route::get('/faq', function () {
    return view('welcome');
})->name('faq');

// Single Unified Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'webLogin']);

    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');

    Route::post('/register', function (Request $request) {
        $validated = $request->validate([
            'full_name' => 'required|string|min:3|max:255',
            'father_name' => 'required|string|min:3|max:255',
            'cnic' => 'required|string|size:15|unique:users,cnic',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|size:12',
            'password' => ['required', 'string', 'confirmed', \Illuminate\Validation\Rules\Password::min(8)],
        ]);

        $user = User::create([
            'full_name' => trim($validated['full_name']),
            'father_name' => trim($validated['father_name']),
            'cnic' => $validated['cnic'],
            'email' => strtolower(trim($validated['email'])),
            'phone' => $validated['phone'],
            'password' => $validated['password'],
            'role' => 'STUDENT',
            'is_verified' => true,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('student.dashboard')->with('success', 'Registration successful! Welcome to SALU Exam Portal.');
    });

    Route::get('/forgot-password', function () {
        return view('auth.login');
    })->name('password.request');

    Route::get('/reset-password', function () {
        return view('auth.login');
    })->name('password.reset');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'webLogout'])->name('logout');

    Route::get('/profile', function () {
        $role = auth()->user()->role;
        if ($role === 'STUDENT') {
            return view('student.profile');
        }
        return redirect()->route('admin.dashboard');
    })->name('profile');
});

// Email verification
Route::middleware(['auth', 'check.role:STUDENT'])->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.login');
    })->name('verification.notice');
});

// Student routes (StudentController)
Route::middleware(['auth', 'check.role:STUDENT'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        $myEnrollment = Enrollment::where('user_id', $user->id)
            ->with(['academicYear', 'college', 'fees', 'seat', 'admitCard', 'results'])
            ->latest()
            ->first();
        $latestFee = $myEnrollment?->fees()->latest()->first();
        $isWindowOpen = EnrollmentWindow::where('is_open', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->exists();
        $myAdmitCard = $myEnrollment?->admitCard;
        $myResults = $myEnrollment?->results()->whereNotNull('published_at')->get();

        return view('student.dashboard', compact(
            'myEnrollment', 'latestFee', 'isWindowOpen', 'myAdmitCard', 'myResults'
        ));
    })->name('dashboard');

    Route::get('/profile', function () {
        $user = auth()->user()->load('college');
        return view('student.profile', compact('user'));
    })->name('profile');

    Route::get('/enrollments', function () {
        $user = auth()->user();
        $enrollments = Enrollment::where('user_id', $user->id)
            ->with(['academicYear', 'college', 'fees', 'admitCard', 'results'])
            ->latest()
            ->get();
        return view('student.enrollments', compact('enrollments'));
    })->name('enrollments');

    Route::get('/enrollments/create', function () {
        return view('enrollment.create');
    })->name('enrollments.create');

    Route::get('/enrollments/{id}', function ($id) {
        $enrollment = Enrollment::where('id', $id)
            ->where('user_id', auth()->id())
            ->with(['user', 'academicYear', 'college', 'fees', 'seat', 'admitCard', 'results'])
            ->firstOrFail();
        return view('student.enrollment-details', compact('id', 'enrollment'));
    })->name('enrollments.show');

    Route::get('/results', function () {
        $user = auth()->user();
        $enrollments = Enrollment::where('user_id', $user->id)
            ->with(['results' => fn ($q) => $q->whereNotNull('published_at')])
            ->get();
        return view('student.results', compact('enrollments'));
    })->name('results');

    Route::get('/fees', function () {
        $user = auth()->user();
        $fees = Fee::whereHas('enrollment', fn ($q) => $q->where('user_id', $user->id))
            ->with('enrollment')
            ->latest()
            ->get();
        return view('student.fees', compact('fees'));
    })->name('fees');
});

// Enrollment web routes
Route::middleware(['auth', 'check.role:STUDENT'])->prefix('enrollment')->name('enrollment.')->group(function () {
    Route::get('/create', function () {
        $activeYear = AcademicYear::where('is_active', true)->first();
        $colleges = College::where('is_active', true)->get();
        $collegeMap = $colleges->keyBy('name');

        $csvPath = base_path('college-program-list.csv');
        $districtCollegeProgramData = [];

        if (file_exists($csvPath)) {
            $file = fopen($csvPath, 'r');
            $header = fgetcsv($file);
            while (($row = fgetcsv($file)) !== false) {
                if (count($row) < 3) continue;
                $district = trim($row[0]);
                $collegeName = trim($row[1]);
                $rawPrograms = trim($row[2]);
                $programs = array_values(array_filter(array_map('trim', explode(',', $rawPrograms))));

                if (empty($district) || empty($collegeName)) continue;

                if (!isset($districtCollegeProgramData[$district])) {
                    $districtCollegeProgramData[$district] = [];
                }

                $collegeModel = $collegeMap->get($collegeName);

                $districtCollegeProgramData[$district][] = [
                    'id' => $collegeModel ? $collegeModel->id : '',
                    'name' => $collegeName,
                    'programs' => $programs,
                ];
            }
            fclose($file);
        }

        return view('enrollment.create', compact('activeYear', 'colleges', 'districtCollegeProgramData'));
    })->name('create');

    Route::post('/', function (Request $request, FileUploadService $fileUploadService) {
        $user = $request->user();
        $activeYear = AcademicYear::where('is_active', true)->first() ?? AcademicYear::latest()->first();

        $validated = $request->validate([
            'program' => 'required|string|max:100',
            'session' => 'nullable|string|max:50',
            'semester' => 'nullable|string|max:20',
            'father_name' => 'required|string|max:255',
            'surname' => 'nullable|string|max:100',
            'so_do_wo' => 'nullable|string|max:50',
            'dob' => 'nullable|date',
            'gender' => 'required|in:MALE,FEMALE,OTHER',
            'address' => 'required|string',
            'city' => 'nullable|string|max:100',
            'contact_number' => 'nullable|string|max:20',
            'postal_address' => 'nullable|string',
            'passing_year' => 'nullable|string|max:20',
            'division_obtained' => 'nullable|string|max:50',
            'name_of_board' => 'nullable|string|max:100',
            'nationality' => 'nullable|string|max:50',
            'religion' => 'nullable|string|max:50',
            'domicile_province' => 'nullable|string|max:50',
            'domicile_district' => 'nullable|string|max:50',
            'college_id' => 'nullable|uuid|exists:colleges,id',
            'photo' => 'nullable|image|max:2048',
        ]);

        $photoUrl = null;
        if ($request->hasFile('photo')) {
            $photoUrl = $fileUploadService->uploadStudentPhoto($request->file('photo'), $user->id);
        }

        $matricRecord = [
            'level' => 'Matric / SSC',
            'group' => $request->input('matric_group', 'Science'),
            'board' => $request->input('matric_board', 'BISE Sukkur'),
            'passing_year' => $request->input('matric_passing_year', '2022'),
            'roll_no' => $request->input('matric_roll_no', ''),
            'total_marks' => (int) $request->input('matric_total_marks', 1100),
            'obtained_marks' => (int) $request->input('matric_obtained_marks', 0),
            'percentage' => $request->input('matric_percentage', '0%'),
            'grade' => $request->input('matric_grade', 'A-1'),
        ];

        $interRecord = [
            'level' => 'Intermediate / HSC',
            'group' => $request->input('inter_group', 'Pre-Engineering'),
            'board' => $request->input('inter_board', $request->input('name_of_board', 'BISE Sukkur')),
            'passing_year' => $request->input('inter_passing_year', $request->input('passing_year', '2024')),
            'roll_no' => $request->input('inter_roll_no', ''),
            'total_marks' => (int) $request->input('inter_total_marks', 1100),
            'obtained_marks' => (int) $request->input('inter_obtained_marks', 0),
            'percentage' => $request->input('inter_percentage', '0%'),
            'grade' => $request->input('inter_grade', $request->input('division_obtained', 'A-1')),
        ];

        $documents = [];
        if ($request->hasFile('doc_cnic')) {
            $documents['cnic'] = $fileUploadService->uploadDocument($request->file('doc_cnic'), $user->id, 'cnic');
        }
        if ($request->hasFile('doc_matric')) {
            $documents['matric'] = $fileUploadService->uploadDocument($request->file('doc_matric'), $user->id, 'matric');
        }
        if ($request->hasFile('doc_inter')) {
            $documents['intermediate'] = $fileUploadService->uploadDocument($request->file('doc_inter'), $user->id, 'intermediate');
        }

        $enrollment = Enrollment::create([
            'user_id' => $user->id,
            'academic_year_id' => $activeYear?->id,
            'college_id' => $validated['college_id'] ?? null,
            'program' => $validated['program'],
            'session' => $validated['session'] ?? now()->format('Y') . '-' . (now()->year + 4),
            'semester' => $validated['semester'] ?? '1',
            'father_name' => $validated['father_name'],
            'surname' => $validated['surname'] ?? null,
            'so_do_wo' => $validated['so_do_wo'] ?? null,
            'dob' => $validated['dob'] ?? now()->subYears(18)->toDateString(),
            'gender' => $validated['gender'],
            'address' => $validated['address'],
            'city' => $validated['city'] ?? null,
            'contact_number' => $validated['contact_number'] ?? $user->phone,
            'postal_address' => $validated['postal_address'] ?? null,
            'passing_year' => $interRecord['passing_year'] ?? $validated['passing_year'] ?? null,
            'division_obtained' => $interRecord['grade'] ?? $validated['division_obtained'] ?? null,
            'name_of_board' => $interRecord['board'] ?? $validated['name_of_board'] ?? null,
            'nationality' => $validated['nationality'] ?? 'Pakistani',
            'religion' => $validated['religion'] ?? 'Islam',
            'domicile_province' => $validated['domicile_province'] ?? 'Sindh',
            'domicile_district' => $validated['domicile_district'] ?? null,
            'academic_records' => $academicRecords,
            'documents' => $documents,
            'photo_url' => $photoUrl,
            'status' => 'PENDING',
        ]);

        Fee::create([
            'enrollment_id' => $enrollment->id,
            'challan_number' => Fee::generateChallanNumber(),
            'amount' => 1500.00,
            'status' => 'UNPAID',
            'due_date' => now()->addDays(7),
        ]);

        return redirect()->route('student.dashboard')->with('success', 'Enrollment application submitted successfully! Please proceed to fee payment.');
    })->name('store');

    Route::get('/card', function () {
        $user = auth()->user();
        $enrollments = Enrollment::where('user_id', $user->id)->with('college')->get();
        return view('student.enrollments', compact('enrollments'));
    })->name('card');

    Route::get('/{id}', function ($id) {
        $enrollment = Enrollment::where('id', $id)
            ->where('user_id', auth()->id())
            ->with(['user', 'academicYear', 'college', 'fees', 'seat', 'admitCard', 'results'])
            ->firstOrFail();
        return view('student.enrollment-details', compact('id', 'enrollment'));
    })->name('details');

    Route::get('/{feeId}/challan-pdf', [StudentController::class, 'downloadChallan'])->name('challan-pdf');
    Route::get('/{enrollmentId}/admit-card-pdf', [StudentController::class, 'downloadAdmitCard'])->name('admit-card-pdf');
    Route::get('/{enrollmentId}/result-card-pdf', [StudentController::class, 'downloadResultCard'])->name('result-card-pdf');
});

// Exams routes
Route::middleware(['auth', 'check.role:STUDENT'])->prefix('exams')->name('exams.')->group(function () {
    Route::get('/form', function () {
        return view('student.dashboard');
    })->name('form');

    Route::get('/fee-challan', function () {
        return view('student.fees');
    })->name('fee-challan');

    Route::get('/admit-card', function () {
        return view('student.results');
    })->name('admit-card');

    Route::get('/results', function () {
        return view('student.results');
    })->name('results');
});

// Payment checkout routes
Route::middleware(['auth', 'check.role:STUDENT'])->prefix('payment')->name('payment.')->group(function () {
    Route::get('/{feeId}/checkout', function ($feeId) {
        $fee = Fee::with('enrollment.user')->findOrFail($feeId);
        if ($fee->enrollment->user_id !== auth()->id()) {
            abort(403);
        }
        return view('student.fees', compact('fee'));
    })->name('checkout');

    Route::post('/{feeId}/submit', function (Request $request, $feeId) {
        $fee = Fee::with('enrollment')->findOrFail($feeId);
        if ($fee->enrollment->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'transaction_id' => 'required|string|max:100',
            'payment_method' => 'required|string|in:JazzCash,EasyPaisa,BankTransfer,ONLINE,BANK',
        ]);

        $fee->update([
            'transaction_id' => $validated['transaction_id'],
            'payment_method' => $validated['payment_method'],
            'status' => 'PAID',
            'paid_at' => now(),
        ]);

        AuditLog::log(auth()->id(), 'SUBMIT_FEE_PAYMENT', 'Fee', $feeId, "Payment submitted: {$validated['transaction_id']}", request()->ip());

        return redirect()->route('student.dashboard')->with('success', 'Payment recorded successfully!');
    })->name('submit');
});

// Admin routes (AdminController)
Route::middleware(['auth', 'check.role:ADMIN,SUPERADMIN'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        $isCollegeAdmin = $user->role === 'COLLEGE_ADMIN';
        $collegeId = $user->college_id;
        $collegeName = $user->college?->name ?? 'Main Campus';

        $enrollmentsQuery = Enrollment::query();
        if ($isCollegeAdmin && $collegeId) {
            $enrollmentsQuery->where('college_id', $collegeId);
        }

        $totalEnrollments = (clone $enrollmentsQuery)->count();
        $pendingEnrollments = (clone $enrollmentsQuery)->where('status', 'PENDING')->count();
        $approvedEnrollments = (clone $enrollmentsQuery)->where('status', 'APPROVED')->count();
        $rejectedEnrollments = (clone $enrollmentsQuery)->where('status', 'REJECTED')->count();

        $maleApprovedCount = (clone $enrollmentsQuery)->where('status', 'APPROVED')->where('gender', 'MALE')->count();
        $femaleApprovedCount = (clone $enrollmentsQuery)->where('status', 'APPROVED')->where('gender', 'FEMALE')->count();

        $totalStudents = $isCollegeAdmin && $collegeId
            ? User::where('role', 'STUDENT')->where('college_id', $collegeId)->count()
            : User::where('role', 'STUDENT')->count();

        $totalColleges = College::where('is_active', true)->count();
        $totalPrograms = (clone $enrollmentsQuery)->distinct('program')->count('program');
        if ($totalPrograms === 0) {
            $totalPrograms = 12;
        }

        $submittedExamForms = (clone $enrollmentsQuery)->whereIn('status', ['APPROVED', 'PENDING'])->count();

        $paidFeesCount = Fee::whereIn('status', ['PAID', 'VERIFIED'])->count();
        $unpaidFeesCount = Fee::where('status', 'UNPAID')->count();
        $totalRevenue = Fee::whereIn('status', ['PAID', 'VERIFIED'])->sum('amount');
        $recentEnrollments = (clone $enrollmentsQuery)->with(['user', 'college'])->latest()->take(5)->get();
        $activeWindow = EnrollmentWindow::with('academicYear')->where('is_open', true)->first();
        $isExamWindowOpen = $activeWindow ? true : false;
        $activeYearId = $activeWindow?->academic_year_id ?? AcademicYear::where('is_active', true)->first()?->id ?? AcademicYear::latest()->first()?->id;

        return view('admin.dashboard', compact(
            'totalEnrollments', 'pendingEnrollments', 'approvedEnrollments', 'rejectedEnrollments',
            'totalStudents', 'totalColleges', 'totalPrograms', 'submittedExamForms',
            'paidFeesCount', 'unpaidFeesCount', 'totalRevenue',
            'recentEnrollments', 'activeWindow', 'isCollegeAdmin', 'isExamWindowOpen',
            'collegeName', 'collegeId', 'activeYearId', 'maleApprovedCount', 'femaleApprovedCount'
        ));
    })->name('dashboard');

    // Superadmin specific routes (SuperAdminController)
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
    Route::post('/enrollments/{id}/approve', function (Request $request, $id) {
        $enrollment = Enrollment::with('user')->findOrFail($id);
        $enrollment->status = 'APPROVED';
        $enrollment->rejection_reason = null;
        if (empty($enrollment->roll_number)) {
            $enrollment->roll_number = $enrollment->generateRollNumber();
        }
        $enrollment->save();

        \App\Jobs\SendEnrollmentNotificationJob::dispatch($enrollment, 'approved');
        AuditLog::log(auth()->id(), 'APPROVE_ENROLLMENT', 'Enrollment', $id, "Approved roll: {$enrollment->roll_number}", request()->ip());

        return back()->with('success', "Enrollment approved! Assigned roll number: {$enrollment->roll_number}");
    })->name('enrollments.approve');

    Route::post('/enrollments/{id}/reject', function (Request $request, $id) {
        $enrollment = Enrollment::with('user')->findOrFail($id);
        $enrollment->status = 'REJECTED';
        $enrollment->rejection_reason = $request->input('reason', 'Application rejected by administration');
        $enrollment->save();

        \App\Jobs\SendEnrollmentNotificationJob::dispatch($enrollment, 'rejected');
        AuditLog::log(auth()->id(), 'REJECT_ENROLLMENT', 'Enrollment', $id, "Reason: {$enrollment->rejection_reason}", request()->ip());

        return back()->with('success', 'Enrollment application rejected.');
    })->name('enrollments.reject');

    Route::get('/enrollments', function () {
        $enrollments = Enrollment::with(['user', 'academicYear', 'college'])->latest()->paginate(20);
        return view('admin.enrollments', compact('enrollments'));
    })->name('enrollments');

    Route::get('/enrollments/index', function () {
        $enrollments = Enrollment::with(['user', 'academicYear', 'college'])->latest()->paginate(20);
        return view('admin.enrollments', compact('enrollments'));
    })->name('enrollments.index');

    Route::get('/enrollments/{id}', function ($id) {
        $enrollment = Enrollment::with(['user', 'academicYear', 'college', 'fees', 'seat', 'admitCard', 'results'])->findOrFail($id);
        return view('admin.enrollment-details', compact('id', 'enrollment'));
    })->name('enrollments.show');

    Route::get('/students', function () {
        $students = User::where('role', 'STUDENT')->with('college')->latest()->paginate(20);
        return view('admin.students', compact('students'));
    })->name('students');

    Route::get('/students/index', function () {
        $students = User::where('role', 'STUDENT')->with('college')->latest()->paginate(20);
        return view('admin.students', compact('students'));
    })->name('students.index');

    Route::get('/colleges', function () {
        $colleges = College::latest()->paginate(20);
        return view('admin.colleges', compact('colleges'));
    })->name('colleges');

    Route::get('/colleges/index', function () {
        $colleges = College::latest()->paginate(20);
        return view('admin.colleges', compact('colleges'));
    })->name('colleges.index');

    Route::get('/colleges/create', function () {
        return view('admin.college-create');
    })->name('colleges.create');

    Route::get('/colleges/{id}/edit', function ($id) {
        $college = College::findOrFail($id);
        return view('admin.college-edit', compact('id', 'college'));
    })->name('colleges.edit');

    Route::get('/academic-years', function () {
        $years = AcademicYear::with('enrollmentWindow')->orderByDesc('start_date')->get();
        return view('admin.academic-years', compact('years'));
    })->name('academic-years');

    Route::get('/fees', function () {
        $fees = Fee::with(['enrollment.user'])->latest()->paginate(20);
        return view('admin.fees', compact('fees'));
    })->name('fees');

    Route::get('/fees/index', function () {
        $fees = Fee::with(['enrollment.user'])->latest()->paginate(20);
        return view('admin.fees', compact('fees'));
    })->name('fees.index');

    Route::get('/fees/verification', function () {
        $fees = Fee::with(['enrollment.user'])->whereIn('status', ['PAID', 'UNPAID'])->latest()->paginate(20);
        return view('admin.fees', compact('fees'));
    })->name('fees.verification');

    Route::get('/seats/index', function () {
        return redirect()->route('admin.dashboard');
    })->name('seats.index');

    Route::get('/exams/index', function () {
        return redirect()->route('admin.dashboard');
    })->name('exams.index');

    Route::get('/results/index', function () {
        return redirect()->route('admin.dashboard');
    })->name('results.index');

    Route::get('/reports', function () {
        return view('admin.reports');
    })->name('reports');

    Route::get('/reports/index', function () {
        return view('admin.reports');
    })->name('reports.index');

    Route::get('/audit-logs', function () {
        $logs = AuditLog::with('user')->latest()->paginate(50);
        return view('admin.audit-logs', compact('logs'));
    })->name('audit-logs');

    Route::get('/users/index', [SuperAdminController::class, 'users'])
        ->name('users.index')
        ->middleware('check.role:SUPERADMIN');
});
