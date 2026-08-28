<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Fee;
use App\Services\PdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password as PasswordRule;

class StudentController extends Controller
{
    /**
     * Get student dashboard data
     */
    public function dashboard(Request $request)
    {
        $user = $request->user();

        $enrollments = Enrollment::where('user_id', $user->id)
            ->with(['academicYear', 'fees', 'admitCard', 'results'])
            ->orderByDesc('created_at')
            ->get();

        $latest = $enrollments->first();

        return response()->json([
            'student' => [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'cnic' => $user->cnic,
                'phone' => $user->phone,
            ],
            'stats' => [
                'total_applications' => $enrollments->count(),
                'approved' => $enrollments->where('status', 'APPROVED')->count(),
                'pending' => $enrollments->where('status', 'PENDING')->count(),
                'rejected' => $enrollments->where('status', 'REJECTED')->count(),
            ],
            'latest_enrollment' => $latest ? [
                'id' => $latest->id,
                'program' => $latest->program,
                'session' => $latest->session,
                'semester' => $latest->semester,
                'status' => $latest->status,
                'roll_number' => $latest->roll_number,
                'academic_year' => $latest->academicYear->name,
                'has_admit_card' => $latest->admitCard !== null,
                'has_fee' => $latest->fees->isNotEmpty(),
                'fee_status' => $latest->fees->first()?->status,
                'has_results' => $latest->results->isNotEmpty(),
                'created_at' => $latest->created_at,
            ] : null,
        ]);
    }

    /**
     * Get student profile
     */
    public function profile(Request $request)
    {
        $user = $request->user();
        $user->load('college');

        return response()->json([
            'id' => $user->id,
            'full_name' => $user->full_name,
            'father_name' => $user->father_name,
            'email' => $user->email,
            'cnic' => $user->cnic,
            'phone' => $user->phone,
            'college' => $user->college ? [
                'id' => $user->college->id,
                'name' => $user->college->name,
            ] : null,
            'is_verified' => $user->is_verified,
            'role' => $user->role,
            'created_at' => $user->created_at,
        ]);
    }

    /**
     * Update student profile
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'full_name' => 'sometimes|string|min:3|max:255',
            'father_name' => 'sometimes|string|min:3|max:255',
            'phone' => 'sometimes|string|size:12',
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully.',
            'user' => $user->fresh(),
        ]);
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => ['required', 'string', PasswordRule::min(8)],
        ]);

        if (! Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect.',
            ], 422);
        }

        $user->password = $validated['new_password'];
        $user->save();

        return response()->json([
            'message' => 'Password changed successfully.',
        ]);
    }

    /**
     * Get all enrollments for the student
     */
    public function enrollments(Request $request)
    {
        $user = $request->user();

        $enrollments = Enrollment::where('user_id', $user->id)
            ->with(['academicYear', 'college', 'fees', 'admitCard', 'results'])
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($e) {
                return [
                    'id' => $e->id,
                    'program' => $e->program,
                    'session' => $e->session,
                    'semester' => $e->semester,
                    'status' => $e->status,
                    'roll_number' => $e->roll_number,
                    'father_name' => $e->father_name,
                    'dob' => $e->dob,
                    'gender' => $e->gender,
                    'address' => $e->address,
                    'contact_number' => $e->contact_number,
                    'name_of_board' => $e->name_of_board,
                    'domicile_district' => $e->domicile_district,
                    'domicile_province' => $e->domicile_province,
                    'rejection_reason' => $e->rejection_reason,
                    'college' => $e->college?->name,
                    'academic_year' => $e->academicYear->name,
                    'has_fee' => $e->fees->isNotEmpty(),
                    'fee_paid' => $e->fees->whereIn('status', ['PAID', 'VERIFIED'])->isNotEmpty(),
                    'has_admit_card' => $e->admitCard !== null,
                    'has_results' => $e->results->isNotEmpty(),
                    'created_at' => $e->created_at,
                ];
            });

        return response()->json($enrollments);
    }

    /**
     * Get single enrollment
     */
    public function getEnrollment(Request $request, string $id)
    {
        $user = $request->user();

        $enrollment = Enrollment::where('id', $id)
            ->where('user_id', $user->id)
            ->with(['user', 'academicYear', 'college', 'fees', 'seat', 'admitCard', 'results'])
            ->firstOrFail();

        return response()->json([
            'id' => $enrollment->id,
            'program' => $enrollment->program,
            'session' => $enrollment->session,
            'semester' => $enrollment->semester,
            'father_name' => $enrollment->father_name,
            'dob' => $enrollment->dob,
            'gender' => $enrollment->gender,
            'address' => $enrollment->address,
            'city' => $enrollment->city,
            'contact_number' => $enrollment->contact_number,
            'nationality' => $enrollment->nationality,
            'religion' => $enrollment->religion,
            'domicile_province' => $enrollment->domicile_province,
            'domicile_district' => $enrollment->domicile_district,
            'passing_year' => $enrollment->passing_year,
            'division_obtained' => $enrollment->division_obtained,
            'name_of_board' => $enrollment->name_of_board,
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
            'admit_card_id' => $enrollment->admitCard?->id,
            'results' => $enrollment->results->map(fn ($r) => [
                'subject_code' => $r->subject_code,
                'subject_name' => $r->subject_name,
                'marks' => $r->marks,
                'total_marks' => $r->total_marks,
                'grade' => $r->grade,
                'published_at' => $r->published_at,
            ]),
            'created_at' => $enrollment->created_at,
        ]);
    }

    /**
     * Get results for an enrollment
     */
    public function getResults(Request $request, string $enrollmentId)
    {
        $user = $request->user();

        $enrollment = Enrollment::where('id', $enrollmentId)
            ->where('user_id', $user->id)
            ->with(['results' => function ($query) {
                $query->whereNotNull('published_at');
            }])
            ->firstOrFail();

        if ($enrollment->results->isEmpty()) {
            return response()->json([
                'message' => 'Results not published yet.',
                'results' => [],
            ]);
        }

        $totalMarks = $enrollment->results->sum('total_marks');
        $obtainedMarks = $enrollment->results->sum('marks');
        $percentage = $totalMarks > 0 ? round(($obtainedMarks / $totalMarks) * 100, 2) : 0;

        return response()->json([
            'roll_number' => $enrollment->roll_number,
            'program' => $enrollment->program,
            'session' => $enrollment->session,
            'results' => $enrollment->results->map(fn ($r) => [
                'subject_code' => $r->subject_code,
                'subject_name' => $r->subject_name,
                'marks' => $r->marks,
                'total_marks' => $r->total_marks,
                'grade' => $r->grade,
                'published_at' => $r->published_at,
            ]),
            'total_marks' => $totalMarks,
            'obtained_marks' => $obtainedMarks,
            'percentage' => $percentage,
        ]);
    }

    /**
     * Download fee challan
     */
    public function downloadChallan(Request $request, string $feeId, PdfService $pdfService)
    {
        $user = $request->user();

        $fee = Fee::with(['enrollment.user', 'enrollment.college'])->findOrFail($feeId);
        $enrollment = $fee->enrollment;

        if ($user->role === 'STUDENT' && $enrollment->user_id !== $user->id) {
            abort(403, 'Unauthorized access to this challan.');
        }

        if ($user->role === 'COLLEGE_ADMIN' && $user->college_id && $enrollment->college_id !== $user->college_id) {
            abort(403, 'Unauthorized access to this challan.');
        }

        $pdf = $pdfService->generateChallan($fee, $enrollment, $enrollment->user);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "inline; filename=\"challan-{$fee->challan_number}.pdf\"",
        ]);
    }

    /**
     * Download admit card
     */
    public function downloadAdmitCard(Request $request, string $enrollmentId, PdfService $pdfService)
    {
        $user = $request->user();

        $enrollment = Enrollment::with(['user', 'admitCard.seat', 'college'])->findOrFail($enrollmentId);

        if ($user->role === 'STUDENT' && $enrollment->user_id !== $user->id) {
            abort(403, 'Unauthorized access to this admit card.');
        }

        if ($user->role === 'COLLEGE_ADMIN' && $user->college_id && $enrollment->college_id !== $user->college_id) {
            abort(403, 'Unauthorized access to this admit card.');
        }

        if (! $enrollment->admitCard) {
            return response()->json([
                'message' => 'Admit card not available yet. Please wait for approval.',
            ], 404);
        }

        $pdf = $pdfService->generateAdmitCard($enrollment->admitCard, $enrollment, $enrollment->user);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "inline; filename=\"admit-card-{$enrollment->roll_number}.pdf\"",
        ]);
    }

    /**
     * Download result card
     */
    public function downloadResultCard(Request $request, string $enrollmentId, PdfService $pdfService)
    {
        $user = $request->user();

        $enrollment = Enrollment::with([
            'user',
            'results' => function ($query) {
                $query->whereNotNull('published_at');
            },
            'college',
        ])->findOrFail($enrollmentId);

        if ($user->role === 'STUDENT' && $enrollment->user_id !== $user->id) {
            abort(403, 'Unauthorized access to this result card.');
        }

        if ($user->role === 'COLLEGE_ADMIN' && $user->college_id && $enrollment->college_id !== $user->college_id) {
            abort(403, 'Unauthorized access to this result card.');
        }

        $pdf = $pdfService->generateResultCard($enrollment, $enrollment->results->toArray(), $enrollment->user);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "inline; filename=\"result-card-{$enrollment->roll_number}.pdf\"",
        ]);
    }

    /**
     * Download application form
     */
    public function downloadApplicationForm(Request $request, string $enrollmentId, PdfService $pdfService)
    {
        $user = $request->user();

        $enrollment = Enrollment::with(['user', 'college', 'academicYear'])->findOrFail($enrollmentId);

        if ($user->role === 'STUDENT' && $enrollment->user_id !== $user->id) {
            abort(403, 'Unauthorized access to this application form.');
        }

        if ($user->role === 'COLLEGE_ADMIN' && $user->college_id && $enrollment->college_id !== $user->college_id) {
            abort(403, 'Unauthorized access to this application form.');
        }

        $pdf = $pdfService->generateApplicationForm($enrollment, $enrollment->user);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "inline; filename=\"application-form-{$enrollment->id}.pdf\"",
        ]);
    }

    /**
     * Download enrollment / registration card
     */
    public function downloadEnrollmentCard(Request $request, string $enrollmentId, PdfService $pdfService)
    {
        $user = $request->user();

        $enrollment = Enrollment::with(['user', 'college', 'academicYear'])->findOrFail($enrollmentId);

        if ($user->role === 'STUDENT' && $enrollment->user_id !== $user->id) {
            abort(403, 'Unauthorized access to this registration card.');
        }

        if ($user->role === 'COLLEGE_ADMIN' && $user->college_id && $enrollment->college_id !== $user->college_id) {
            abort(403, 'Unauthorized access to this registration card.');
        }

        $pdf = $pdfService->generateEnrollmentCard($enrollment, $enrollment->user);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "inline; filename=\"enrollment-card-{$enrollment->roll_number}.pdf\"",
        ]);
    }

    /**
     * Web Student Dashboard view
     */
    public function webDashboard(Request $request)
    {
        $user = $request->user();
        $myEnrollment = Enrollment::where('user_id', $user->id)
            ->with(['academicYear', 'college', 'fees', 'seat', 'admitCard', 'results'])
            ->latest()
            ->first();
        $latestFee = $myEnrollment?->fees()->latest()->first();
        $isWindowOpen = \App\Models\EnrollmentWindow::where('is_open', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->exists();
        $myAdmitCard = $myEnrollment?->admitCard;
        $myResults = $myEnrollment?->results()->whereNotNull('published_at')->get();

        return view('student.dashboard', compact(
            'myEnrollment', 'latestFee', 'isWindowOpen', 'myAdmitCard', 'myResults'
        ));
    }

    /**
     * Web Student Profile view
     */
    public function webProfile(Request $request)
    {
        $user = $request->user()->load('college');
        return view('student.profile', compact('user'));
    }

    /**
     * Web Enrollments list view
     */
    public function webEnrollments(Request $request)
    {
        $user = $request->user();
        $enrollments = Enrollment::where('user_id', $user->id)
            ->with(['academicYear', 'college', 'fees', 'admitCard', 'results'])
            ->latest()
            ->get();

        return view('student.enrollments', compact('enrollments'));
    }

    /**
     * Web Enrollment details view
     */
    public function webEnrollmentDetails(Request $request, string $id)
    {
        $enrollment = Enrollment::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->with(['user', 'academicYear', 'college', 'fees', 'seat', 'admitCard', 'results'])
            ->firstOrFail();

        return view('student.enrollment-details', compact('id', 'enrollment'));
    }

    /**
     * Web Results list view
     */
    public function webResults(Request $request)
    {
        $user = $request->user();
        $enrollments = Enrollment::where('user_id', $user->id)
            ->with(['results' => fn ($q) => $q->whereNotNull('published_at')])
            ->get();

        return view('student.results', compact('enrollments'));
    }

    /**
     * Web Fees list view
     */
    public function webFees(Request $request)
    {
        $user = $request->user();
        $fees = Fee::whereHas('enrollment', fn ($q) => $q->where('user_id', $user->id))
            ->with('enrollment')
            ->latest()
            ->get();

        return view('student.fees', compact('fees'));
    }
}
