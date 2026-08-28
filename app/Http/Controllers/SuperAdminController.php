<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\College;
use App\Models\Enrollment;
use App\Models\EnrollmentWindow;
use App\Models\Fee;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password as PasswordRule;

class SuperAdminController extends Controller
{
    /**
     * Display Superadmin master dashboard
     */
    public function dashboard(Request $request)
    {
        $totalEnrollments = Enrollment::count();
        $pendingEnrollments = Enrollment::where('status', 'PENDING')->count();
        $approvedEnrollments = Enrollment::where('status', 'APPROVED')->count();
        $rejectedEnrollments = Enrollment::where('status', 'REJECTED')->count();
        $totalExamForms = Enrollment::whereIn('status', ['APPROVED', 'PENDING'])->count();

        $totalStudents = User::where('role', 'STUDENT')->count();
        $students = $totalStudents;
        $totalAdmins = User::whereIn('role', ['ADMIN', 'SUPERADMIN'])->count();
        $admins = $totalAdmins;
        $collegeAdmins = User::where('role', 'COLLEGE_ADMIN')->count();
        $totalColleges = College::count();
        $totalUsers = User::count();

        $paidFeesCount = Fee::whereIn('status', ['PAID', 'VERIFIED'])->count();
        $unpaidFeesCount = Fee::where('status', 'UNPAID')->count();
        $totalRevenue = Fee::whereIn('status', ['PAID', 'VERIFIED'])->sum('amount');

        $activeWindow = EnrollmentWindow::with('academicYear')->where('is_open', true)->first();
        $currentWindow = $activeWindow;
        $currentExamWindow = $activeWindow;
        $activeAcademicYear = $activeWindow?->academicYear ?? AcademicYear::where('is_active', true)->first() ?? AcademicYear::latest()->first();
        $isExamWindowOpen = (bool) ($activeWindow?->is_open ?? false);

        $pendingList = Enrollment::with(['user', 'college', 'academicYear'])->where('status', 'PENDING')->latest()->take(10)->get();
        $pendingEnrollmentsList = $pendingList;

        $recentUsers = User::latest()->take(10)->get();
        $recentAuditLogs = AuditLog::with('user')->latest()->take(10)->get();

        if ($request->expectsJson()) {
            return response()->json([
                'stats' => [
                    'total_enrollments' => $totalEnrollments,
                    'pending_enrollments' => $pendingEnrollments,
                    'approved_enrollments' => $approvedEnrollments,
                    'rejected_enrollments' => $rejectedEnrollments,
                    'total_students' => $totalStudents,
                    'total_admins' => $totalAdmins,
                    'total_colleges' => $totalColleges,
                    'total_users' => $totalUsers,
                    'total_revenue' => $totalRevenue,
                ],
                'active_window' => $activeWindow,
                'pending_enrollments' => $pendingList,
            ]);
        }

        return view('admin.superadmin-dashboard', compact(
            'totalEnrollments', 'pendingEnrollments', 'approvedEnrollments', 'rejectedEnrollments', 'totalExamForms',
            'totalStudents', 'students', 'totalAdmins', 'admins', 'collegeAdmins', 'totalColleges', 'totalUsers',
            'totalRevenue', 'paidFeesCount', 'unpaidFeesCount',
            'activeWindow', 'currentWindow', 'currentExamWindow', 'activeAcademicYear', 'isExamWindowOpen',
            'pendingList', 'pendingEnrollmentsList', 'recentUsers', 'recentAuditLogs'
        ));
    }

    /**
     * Get or view system settings
     */
    public function settings(Request $request)
    {
        $settings = SystemSetting::all()->pluck('value', 'key');

        if ($request->expectsJson()) {
            return response()->json($settings);
        }

        return view('admin.settings', compact('settings'));
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

        AuditLog::log(
            $request->user()->id,
            'UPDATE_SYSTEM_SETTINGS',
            'SystemSetting',
            null,
            'Updated global system configuration',
            $request->ip()
        );

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Settings updated successfully.',
            ]);
        }

        return back()->with('success', 'System settings saved successfully.');
    }

    /**
     * Manage all portal users
     */
    public function users(Request $request)
    {
        $query = User::with('college');

        if ($request->filled('role')) {
            $query->where('role', strtoupper($request->role));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'ilike', '%' . $search . '%')
                  ->orWhere('email', 'ilike', '%' . $search . '%')
                  ->orWhere('cnic', 'ilike', '%' . $search . '%');
            });
        }

        $users = $query->latest()->paginate(20);

        if ($request->expectsJson()) {
            return response()->json($users);
        }

        return view('admin.students', compact('users'));
    }

    /**
     * Update user role / permissions
     */
    public function updateUserRole(Request $request, string $id)
    {
        $validated = $request->validate([
            'role' => 'required|in:STUDENT,COLLEGE_ADMIN,ADMIN,SUPERADMIN',
            'college_id' => 'nullable|uuid|exists:colleges,id',
        ]);

        $user = User::findOrFail($id);

        if ($user->id === $request->user()->id) {
            return $request->expectsJson()
                ? response()->json(['message' => 'You cannot change your own role.'], 403)
                : back()->withErrors(['role' => 'You cannot change your own role.']);
        }

        if ($user->role === 'SUPERADMIN' && $validated['role'] !== 'SUPERADMIN'
            && User::where('role', 'SUPERADMIN')->count() <= 1) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Cannot demote the last super admin.'], 400)
                : back()->withErrors(['role' => 'Cannot demote the last super admin.']);
        }

        $oldRole = $user->role;
        $user->role = $validated['role'];
        $user->college_id = $validated['college_id'] ?? $user->college_id;
        $user->save();

        AuditLog::log(
            $request->user()->id,
            'UPDATE_USER_ROLE',
            'User',
            $id,
            "Changed role from {$oldRole} to {$validated['role']}",
            $request->ip()
        );

        if ($request->expectsJson()) {
            return response()->json([
                'message' => "User role updated to {$validated['role']}",
                'user' => $user,
            ]);
        }

        return back()->with('success', "User role successfully changed to {$validated['role']}.");
    }

    /**
     * Master control: Toggle enrollment window
     */
    public function toggleEnrollmentWindow(Request $request)
    {
        $activeYear = AcademicYear::where('is_active', true)->first() ?? AcademicYear::latest()->first();
        $isOpen = $request->boolean('is_open', true);

        EnrollmentWindow::query()->update(['is_open' => false]);

        if ($isOpen && $activeYear) {
            EnrollmentWindow::updateOrCreate(
                ['academic_year_id' => $activeYear->id],
                [
                    'start_date' => now()->subMinutes(5),
                    'end_date' => now()->addMonth(),
                    'is_open' => true,
                ]
            );
        }

        AuditLog::log(
            $request->user()->id,
            'TOGGLE_ENROLLMENT_WINDOW',
            'EnrollmentWindow',
            null,
            $isOpen ? 'Master Enrollment Window opened' : 'Master Enrollment Window closed',
            $request->ip()
        );

        if ($request->expectsJson()) {
            return response()->json([
                'is_open' => $isOpen,
                'message' => $isOpen ? 'Enrollment window is now OPEN.' : 'Enrollment window is now CLOSED.',
            ]);
        }

        return back()->with('success', $isOpen ? 'Enrollment window is now OPEN!' : 'Enrollment window is now CLOSED.');
    }
}
