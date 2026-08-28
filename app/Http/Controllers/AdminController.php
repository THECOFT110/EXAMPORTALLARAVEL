<?php

namespace App\Http\Controllers;

use App\Jobs\SendEnrollmentNotificationJob;
use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\College;
use App\Models\Enrollment;
use App\Models\EnrollmentWindow;
use App\Models\Fee;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function dashboardStats(Request $request)
    {
        $totalEnrollments = Enrollment::count();
        $pending = Enrollment::where('status', 'PENDING')->count();
        $approved = Enrollment::where('status', 'APPROVED')->count();
        $rejected = Enrollment::where('status', 'REJECTED')->count();
        $draft = Enrollment::where('status', 'DRAFT')->count();

        $totalStudents = User::where('role', 'STUDENT')->count();
        $totalColleges = College::where('is_active', true)->count();

        $paidFees = Fee::whereIn('status', ['PAID', 'VERIFIED'])->count();
        $unpaidFees = Fee::where('status', 'UNPAID')->count();
        $totalRevenue = Fee::whereIn('status', ['PAID', 'VERIFIED'])->sum('amount');

        $recentCount = Enrollment::where('created_at', '>=', now()->subDays(7))->count();

        $programStats = Enrollment::select('program', DB::raw('count(*) as count'))
            ->groupBy('program')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        return response()->json([
            'total_enrollments' => $totalEnrollments,
            'pending' => $pending,
            'approved' => $approved,
            'rejected' => $rejected,
            'draft' => $draft,
            'total_students' => $totalStudents,
            'total_colleges' => $totalColleges,
            'paid_fees' => $paidFees,
            'unpaid_fees' => $unpaidFees,
            'total_revenue' => $totalRevenue,
            'recent_count' => $recentCount,
            'program_stats' => $programStats,
        ]);
    }

    /**
     * Get enrollments with filters and pagination
     */
    public function enrollments(Request $request)
    {
        $query = Enrollment::with(['user', 'academicYear', 'college']);

        if ($request->filled('status')) {
            $query->where('status', strtoupper($request->status));
        }

        if ($request->filled('program')) {
            $query->where('program', 'ilike', '%'.$request->program.'%');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('full_name', 'ilike', '%'.$search.'%')
                        ->orWhere('cnic', 'ilike', '%'.$search.'%')
                        ->orWhere('email', 'ilike', '%'.$search.'%');
                })->orWhere('roll_number', 'ilike', '%'.$search.'%');
            });
        }

        $page = $request->get('page', 1);
        $pageSize = $request->get('pageSize', 20);

        $total = $query->count();
        $enrollments = $query->orderByDesc('created_at')
            ->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->get()
            ->map(function ($e) {
                return [
                    'id' => $e->id,
                    'student_name' => $e->user->full_name,
                    'father_name' => $e->father_name,
                    'email' => $e->user->email,
                    'cnic' => $e->user->cnic,
                    'phone' => $e->user->phone,
                    'program' => $e->program,
                    'session' => $e->session,
                    'semester' => $e->semester,
                    'status' => $e->status,
                    'roll_number' => $e->roll_number,
                    'college' => $e->college?->name,
                    'academic_year' => $e->academicYear->name,
                    'photo_url' => $e->photo_url,
                    'created_at' => $e->created_at,
                    'updated_at' => $e->updated_at,
                ];
            });

        return response()->json([
            'data' => $enrollments,
            'total' => $total,
            'page' => $page,
            'page_size' => $pageSize,
            'total_pages' => ceil($total / $pageSize),
        ]);
    }

    /**
     * Get single enrollment
     */
    public function getEnrollment(Request $request, string $id)
    {
        $enrollment = Enrollment::with([
            'user', 'academicYear', 'college',
            'fees', 'seat', 'admitCard', 'results',
        ])->findOrFail($id);

        return response()->json([
            'id' => $enrollment->id,
            'student_name' => $enrollment->user->full_name,
            'father_name' => $enrollment->father_name,
            'surname' => $enrollment->surname,
            'so_do_wo' => $enrollment->so_do_wo,
            'email' => $enrollment->user->email,
            'cnic' => $enrollment->user->cnic,
            'phone' => $enrollment->contact_number ?? $enrollment->user->phone,
            'dob' => $enrollment->dob,
            'gender' => $enrollment->gender,
            'address' => $enrollment->address,
            'city' => $enrollment->city,
            'postal_address' => $enrollment->postal_address,
            'program' => $enrollment->program,
            'session' => $enrollment->session,
            'semester' => $enrollment->semester,
            'passing_year' => $enrollment->passing_year,
            'division_obtained' => $enrollment->division_obtained,
            'last_exam_details' => $enrollment->last_exam_details,
            'name_of_board' => $enrollment->name_of_board,
            'nationality' => $enrollment->nationality,
            'religion' => $enrollment->religion,
            'domicile_province' => $enrollment->domicile_province,
            'domicile_district' => $enrollment->domicile_district,
            'status' => $enrollment->status,
            'roll_number' => $enrollment->roll_number,
            'rejection_reason' => $enrollment->rejection_reason,
            'photo_url' => $enrollment->photo_url,
            'college' => $enrollment->college ? [
                'id' => $enrollment->college->id,
                'name' => $enrollment->college->name,
            ] : null,
            'academic_year' => [
                'id' => $enrollment->academicYear->id,
                'name' => $enrollment->academicYear->name,
            ],
            'fees' => $enrollment->fees->map(fn ($f) => [
                'id' => $f->id,
                'challan_number' => $f->challan_number,
                'amount' => $f->amount,
                'status' => $f->status,
                'due_date' => $f->due_date,
                'paid_at' => $f->paid_at,
            ]),
            'seat' => $enrollment->seat ? [
                'exam_center' => $enrollment->seat->exam_center,
                'room_no' => $enrollment->seat->room_no,
                'seat_no' => $enrollment->seat->seat_no,
            ] : null,
            'has_admit_card' => $enrollment->admitCard !== null,
            'results' => $enrollment->results->map(fn ($r) => [
                'subject_code' => $r->subject_code,
                'subject_name' => $r->subject_name,
                'marks' => $r->marks,
                'total_marks' => $r->total_marks,
                'grade' => $r->grade,
            ]),
            'created_at' => $enrollment->created_at,
            'updated_at' => $enrollment->updated_at,
        ]);
    }

    /**
     * Approve enrollment
     */
    public function approveEnrollment(Request $request, string $id)
    {
        $enrollment = Enrollment::with('user')->findOrFail($id);

        $enrollment->approveWithRollNumber();

        AuditLog::log(
            $request->user()->id,
            'APPROVE_ENROLLMENT',
            'Enrollment',
            $id,
            "Enrollment approved. Roll: {$enrollment->roll_number}",
            $request->ip()
        );

        // Send notification email
        SendEnrollmentNotificationJob::dispatch($enrollment, 'approved');

        return response()->json([
            'message' => 'Enrollment approved.',
            'roll_number' => $enrollment->roll_number,
        ]);
    }

    /**
     * Reject enrollment
     */
    public function rejectEnrollment(Request $request, string $id)
    {
        $validated = $request->validate([
            'reason' => 'required|string',
        ]);

        $enrollment = Enrollment::with('user')->findOrFail($id);

        $enrollment->status = 'REJECTED';
        $enrollment->rejection_reason = $validated['reason'];
        $enrollment->save();

        AuditLog::log(
            $request->user()->id,
            'REJECT_ENROLLMENT',
            'Enrollment',
            $id,
            "Reason: {$validated['reason']}",
            $request->ip()
        );

        // Send notification email
        SendEnrollmentNotificationJob::dispatch($enrollment, 'rejected');

        return response()->json([
            'message' => 'Enrollment rejected.',
        ]);
    }

    /**
     * Bulk approve enrollments
     */
    public function bulkApprove(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'uuid',
        ]);

        $enrollments = Enrollment::with('user')->whereIn('id', $validated['ids'])->get();

        foreach ($enrollments as $enrollment) {
            $enrollment->approveWithRollNumber();

            SendEnrollmentNotificationJob::dispatch($enrollment, 'approved');
        }

        AuditLog::log(
            $request->user()->id,
            'BULK_APPROVE_ENROLLMENTS',
            'Enrollment',
            null,
            "Approved {$enrollments->count()} enrollments in bulk",
            $request->ip()
        );

        return response()->json([
            'message' => "{$enrollments->count()} enrollments approved.",
            'count' => $enrollments->count(),
        ]);
    }

    /**
     * Bulk reject enrollments
     */
    public function bulkReject(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'uuid',
            'reason' => 'nullable|string',
        ]);

        $enrollments = Enrollment::with('user')->whereIn('id', $validated['ids'])->get();
        $reason = $validated['reason'] ?? 'Rejected by administrator';

        foreach ($enrollments as $enrollment) {
            $enrollment->status = 'REJECTED';
            $enrollment->rejection_reason = $reason;
            $enrollment->save();

            SendEnrollmentNotificationJob::dispatch($enrollment, 'rejected');
        }

        AuditLog::log(
            $request->user()->id,
            'BULK_REJECT_ENROLLMENTS',
            'Enrollment',
            null,
            "Rejected {$enrollments->count()} enrollments in bulk. Reason: {$reason}",
            $request->ip()
        );

        return response()->json([
            'message' => "{$enrollments->count()} enrollments rejected.",
            'count' => $enrollments->count(),
        ]);
    }

    /**
     * Get students list
     */
    public function students(Request $request)
    {
        $query = User::where('role', 'STUDENT')->withCount('enrollments')->with('college');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'ilike', '%'.$search.'%')
                    ->orWhere('email', 'ilike', '%'.$search.'%')
                    ->orWhere('cnic', 'ilike', '%'.$search.'%');
            });
        }

        $page = $request->get('page', 1);
        $pageSize = $request->get('pageSize', 20);

        $total = $query->count();
        $students = $query->orderByDesc('created_at')
            ->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->get()
            ->map(function ($u) {
                return [
                    'id' => $u->id,
                    'full_name' => $u->full_name,
                    'father_name' => $u->father_name,
                    'email' => $u->email,
                    'cnic' => $u->cnic,
                    'phone' => $u->phone,
                    'college' => $u->college?->name,
                    'is_verified' => $u->is_verified,
                    'enrollment_count' => $u->enrollments_count,
                    'created_at' => $u->created_at,
                ];
            });

        return response()->json([
            'data' => $students,
            'total' => $total,
            'page' => $page,
            'page_size' => $pageSize,
            'total_pages' => ceil($total / $pageSize),
        ]);
    }

    /**
     * Get audit logs
     */
    public function auditLogs(Request $request)
    {
        $query = AuditLog::with('user');

        if ($request->filled('action')) {
            $query->where('action', 'ilike', '%'.$request->action.'%');
        }

        $page = $request->get('page', 1);
        $pageSize = $request->get('pageSize', 50);

        $total = $query->count();
        $logs = $query->orderByDesc('created_at')
            ->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->get()
            ->map(fn ($a) => [
                'id' => $a->id,
                'user_name' => $a->user?->full_name ?? 'System',
                'action' => $a->action,
                'entity' => $a->entity,
                'entity_id' => $a->entity_id,
                'details' => $a->details,
                'ip_address' => $a->ip_address,
                'created_at' => $a->created_at,
            ]);

        return response()->json([
            'data' => $logs,
            'total' => $total,
            'page' => $page,
            'page_size' => $pageSize,
        ]);
    }

    /**
     * Get academic years
     */
    public function academicYears(Request $request)
    {
        $years = AcademicYear::with('enrollmentWindow')
            ->withCount('enrollments')
            ->orderByDesc('start_date')
            ->get()
            ->map(fn ($a) => [
                'id' => $a->id,
                'name' => $a->name,
                'start_date' => $a->start_date,
                'end_date' => $a->end_date,
                'is_active' => $a->is_active,
                'enrollment_window' => $a->enrollmentWindow ? [
                    'start_date' => $a->enrollmentWindow->start_date,
                    'end_date' => $a->enrollmentWindow->end_date,
                    'is_open' => $a->enrollmentWindow->is_open,
                ] : null,
                'enrollment_count' => $a->enrollments_count,
            ]);

        return response()->json($years);
    }

    /**
     * Create academic year
     */
    public function createAcademicYear(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
        ]);

        if ($validated['is_active'] ?? false) {
            AcademicYear::where('is_active', true)->update(['is_active' => false]);
        }

        $year = AcademicYear::create($validated);

        return response()->json([
            'message' => 'Academic year created.',
            'id' => $year->id,
        ], 201);
    }

    /**
     * Toggle enrollment window
     */
    public function toggleEnrollmentWindow(Request $request)
    {
        $validated = $request->validate([
            'is_open' => 'required|boolean',
        ]);

        $activeYear = AcademicYear::where('is_active', true)->first();

        if (! $activeYear) {
            return response()->json([
                'message' => 'No active academic year configured.',
            ], 400);
        }

        EnrollmentWindow::query()->update(['is_open' => false]);

        if ($validated['is_open']) {
            $window = EnrollmentWindow::firstOrCreate(
                ['academic_year_id' => $activeYear->id],
                [
                    'start_date' => now()->subMinutes(5),
                    'end_date' => now()->addMonth(),
                    'is_open' => true,
                ]
            );

            $window->update([
                'is_open' => true,
                'start_date' => now()->subMinutes(5),
                'end_date' => now()->addMonth(),
            ]);
        }

        return response()->json([
            'message' => $validated['is_open'] ? 'Enrollment window is now OPEN.' : 'Enrollment window is now CLOSED.',
            'is_open' => $validated['is_open'],
            'academic_year' => $activeYear->name,
        ]);
    }

    /**
     * Get enrollment window status
     */
    public function enrollmentWindowStatus(Request $request)
    {
        $window = EnrollmentWindow::with('academicYear')
            ->where('is_open', true)
            ->orderByDesc('start_date')
            ->first();

        return response()->json([
            'is_open' => $window && $window->is_open,
            'academic_year' => $window?->academicYear?->name,
            'start_date' => $window?->start_date,
            'end_date' => $window?->end_date,
        ]);
    }

    /**
     * Get system settings
     */
    public function getSettings(Request $request)
    {
        $settings = SystemSetting::all()->pluck('value', 'key');

        return response()->json($settings);
    }

    /**
     * Update system settings
     */
    public function updateSettings(Request $request)
    {
        $updates = SystemSetting::validateUpdates($request->except(['_token', '_method']));

        foreach ($updates as $key => $value) {
            SystemSetting::set($key, (string) $value);
        }

        return response()->json([
            'message' => 'Settings updated.',
        ]);
    }

    /**
     * Web Admin Dashboard
     */
    public function webDashboard(Request $request)
    {
        $user = $request->user();
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
            ? \App\Models\User::where('role', 'STUDENT')->where('college_id', $collegeId)->count()
            : \App\Models\User::where('role', 'STUDENT')->count();

        $totalColleges = \App\Models\College::where('is_active', true)->count();
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
        $activeYearId = $activeWindow?->academic_year_id ?? \App\Models\AcademicYear::where('is_active', true)->first()?->id ?? \App\Models\AcademicYear::latest()->first()?->id;

        return view('admin.dashboard', compact(
            'totalEnrollments', 'pendingEnrollments', 'approvedEnrollments', 'rejectedEnrollments',
            'totalStudents', 'totalColleges', 'totalPrograms', 'submittedExamForms',
            'paidFeesCount', 'unpaidFeesCount', 'totalRevenue',
            'recentEnrollments', 'activeWindow', 'isCollegeAdmin', 'isExamWindowOpen',
            'collegeName', 'collegeId', 'activeYearId', 'maleApprovedCount', 'femaleApprovedCount'
        ));
    }

    /**
     * Web approve enrollment
     */
    public function webApproveEnrollment(Request $request, string $id)
    {
        $enrollment = Enrollment::with('user')->findOrFail($id);
        $this->authorize('approve', $enrollment);
        $enrollment->approveWithRollNumber();

        \App\Jobs\SendEnrollmentNotificationJob::dispatch($enrollment, 'approved');
        AuditLog::log(auth()->id(), 'APPROVE_ENROLLMENT', 'Enrollment', $id, "Approved roll: {$enrollment->roll_number}", $request->ip());

        return back()->with('success', "Enrollment approved! Assigned roll number: {$enrollment->roll_number}");
    }

    /**
     * Web reject enrollment
     */
    public function webRejectEnrollment(Request $request, string $id)
    {
        $enrollment = Enrollment::with('user')->findOrFail($id);
        $this->authorize('reject', $enrollment);
        $enrollment->status = 'REJECTED';
        $enrollment->rejection_reason = $request->input('reason', 'Application rejected by administration');
        $enrollment->save();

        \App\Jobs\SendEnrollmentNotificationJob::dispatch($enrollment, 'rejected');
        AuditLog::log(auth()->id(), 'REJECT_ENROLLMENT', 'Enrollment', $id, "Reason: {$enrollment->rejection_reason}", $request->ip());

        return back()->with('success', 'Enrollment application rejected.');
    }

    /**
     * Web list enrollments
     */
    public function webEnrollments(Request $request)
    {
        $user = $request->user();
        $query = Enrollment::with(['user', 'academicYear', 'college'])->latest();

        if ($user->role === 'COLLEGE_ADMIN' && $user->college_id) {
            $query->where('college_id', $user->college_id);
        }

        $enrollments = $query->paginate(20);
        return view('admin.enrollments', compact('enrollments'));
    }

    /**
     * Web enrollment details
     */
    public function webEnrollmentDetails(Request $request, string $id)
    {
        $enrollment = Enrollment::with(['user', 'academicYear', 'college', 'fees', 'seat', 'admitCard', 'results'])->findOrFail($id);
        $this->authorize('view', $enrollment);
        return view('admin.enrollment-details', compact('id', 'enrollment'));
    }

    /**
     * Web students list
     */
    public function webStudents(Request $request)
    {
        $user = $request->user();
        $query = \App\Models\User::where('role', 'STUDENT')->with('college')->latest();

        if ($user->role === 'COLLEGE_ADMIN' && $user->college_id) {
            $query->where('college_id', $user->college_id);
        }

        $students = $query->paginate(20);
        return view('admin.students', compact('students'));
    }

    /**
     * Web colleges list
     */
    public function webColleges(Request $request)
    {
        $colleges = \App\Models\College::latest()->paginate(20);
        return view('admin.colleges', compact('colleges'));
    }

    /**
     * Web college create form
     */
    public function webCollegeCreate(Request $request)
    {
        return view('admin.college-create');
    }

    /**
     * Web college edit form
     */
    public function webCollegeEdit(Request $request, string $id)
    {
        $college = \App\Models\College::findOrFail($id);
        return view('admin.college-edit', compact('id', 'college'));
    }

    /**
     * Web academic years list
     */
    public function webAcademicYears(Request $request)
    {
        $years = \App\Models\AcademicYear::with('enrollmentWindow')->orderByDesc('start_date')->get();
        return view('admin.academic-years', compact('years'));
    }

    /**
     * Web fees list
     */
    public function webFees(Request $request)
    {
        $fees = Fee::with(['enrollment.user'])->latest()->paginate(20);
        return view('admin.fees', compact('fees'));
    }

    /**
     * Web fee verification queue
     */
    public function webFeeVerification(Request $request)
    {
        $fees = Fee::with(['enrollment.user'])->whereIn('status', ['PENDING_VERIFICATION', 'PAID', 'UNPAID'])->latest()->paginate(20);
        return view('admin.fees', compact('fees'));
    }

    /**
     * Web reports view
     */
    public function webReports(Request $request)
    {
        return view('admin.reports');
    }

    /**
     * Web audit logs view
     */
    public function webAuditLogs(Request $request)
    {
        $logs = AuditLog::with('user')->latest()->paginate(50);
        return view('admin.audit-logs', compact('logs'));
    }
}
