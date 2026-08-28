<?php

namespace App\Http\Controllers;

use App\Models\College;
use Illuminate\Http\Request;

class CollegeController extends Controller
{
    /**
     * Get all colleges
     */
    public function index(Request $request)
    {
        $query = College::query();

        if ($request->filled('active_only')) {
            $query->where('is_active', true);
        }

        if ($request->filled('type')) {
            $query->where('type', strtoupper($request->type));
        }

        if ($request->filled('district')) {
            $query->where('district', $request->district);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', '%'.$search.'%')
                    ->orWhere('code', 'ilike', '%'.$search.'%')
                    ->orWhere('city', 'ilike', '%'.$search.'%');
            });
        }

        $colleges = $query->orderBy('name')->get();

        return response()->json($colleges);
    }

    /**
     * Get single college
     */
    public function show(Request $request, string $id)
    {
        $college = College::findOrFail($id);

        return response()->json($college);
    }

    /**
     * Create college (admin only)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:colleges,code',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'principal_name' => 'nullable|string',
            'type' => 'required|in:BOYS,GIRLS,COED',
            'boys_capacity' => 'required|integer|min:0',
            'girls_capacity' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $college = College::create($validated);

        return response()->json([
            'message' => 'College created successfully.',
            'college' => $college,
        ], 201);
    }

    /**
     * Update college (admin only)
     */
    public function update(Request $request, string $id)
    {
        $college = College::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'code' => 'sometimes|string|max:50|unique:colleges,code,'.$id,
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'principal_name' => 'nullable|string',
            'type' => 'sometimes|in:BOYS,GIRLS,COED',
            'boys_capacity' => 'sometimes|integer|min:0',
            'girls_capacity' => 'sometimes|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $college->update($validated);

        return response()->json([
            'message' => 'College updated successfully.',
            'college' => $college,
        ]);
    }

    /**
     * Delete college (admin only)
     */
    public function destroy(Request $request, string $id)
    {
        $college = College::findOrFail($id);

        // Check if college has enrollments
        if ($college->enrollments()->exists()) {
            return response()->json([
                'message' => 'Cannot delete college with existing enrollments.',
            ], 400);
        }

        $college->delete();

        return response()->json([
            'message' => 'College deleted successfully.',
        ]);
    }

    /**
     * Get college statistics
     */
    public function statistics(Request $request, string $id)
    {
        $college = College::with(['enrollments' => function ($query) {
            $query->where('status', 'APPROVED');
        }])->findOrFail($id);

        $maleCount = $college->enrollments->where('gender', 'MALE')->count();
        $femaleCount = $college->enrollments->where('gender', 'FEMALE')->count();

        return response()->json([
            'college' => [
                'id' => $college->id,
                'name' => $college->name,
                'code' => $college->code,
                'type' => $college->type,
            ],
            'capacity' => [
                'boys' => $college->boys_capacity,
                'girls' => $college->girls_capacity,
                'total' => $college->boys_capacity + $college->girls_capacity,
            ],
            'allocated' => [
                'boys' => $maleCount,
                'girls' => $femaleCount,
                'total' => $maleCount + $femaleCount,
            ],
            'available' => [
                'boys' => max(0, $college->boys_capacity - $maleCount),
                'girls' => max(0, $college->girls_capacity - $femaleCount),
            ],
        ]);
    }

    /**
     * Download College Seat Allocation List PDF
     */
    public function downloadSeatListPdf(Request $request, string $collegeId, \App\Services\PdfService $pdfService)
    {
        $college = College::findOrFail($collegeId);
        $academicYearId = $request->query('academicYearId', $request->input('academicYearId'));
        $gender = $request->query('gender', $request->input('gender', '1'));

        $academicYear = \App\Models\AcademicYear::find($academicYearId) ?? \App\Models\AcademicYear::where('is_active', true)->first() ?? 'Academic Session';
        $genderStr = ($gender == '1' || strtoupper((string)$gender) === 'MALE' || strtoupper((string)$gender) === 'BOYS') ? 'MALE' : 'FEMALE';

        $enrollments = \App\Models\Enrollment::where('college_id', $collegeId)
            ->where(function ($q) use ($academicYearId) {
                if ($academicYearId && strlen((string)$academicYearId) > 10) {
                    $q->where('academic_year_id', $academicYearId);
                }
            })
            ->where('gender', $genderStr)
            ->where('status', 'APPROVED')
            ->with(['user', 'seat'])
            ->get();

        $students = $enrollments->map(fn ($e) => [
            'seat_no' => $e->seat?->seat_no ?? 'N/A',
            'room_no' => $e->seat?->room_no ?? 'Hall A',
            'roll_number' => $e->roll_number ?? 'Pending',
            'name' => $e->user->full_name,
            'program' => $e->program,
        ])->toArray();

        $pdf = $pdfService->generateCollegeSeatListPdf($college, $academicYear, $genderStr, $students);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "inline; filename=\"seat-list-{$college->code}-{$genderStr}.pdf\"",
        ]);
    }

    /**
     * Download Complete College Enrollment Register PDF
     */
    public function downloadCompleteListPdf(Request $request, string $collegeId, \App\Services\PdfService $pdfService)
    {
        $college = College::findOrFail($collegeId);
        $academicYearId = $request->query('academicYearId', $request->input('academicYearId'));

        $academicYear = \App\Models\AcademicYear::find($academicYearId) ?? \App\Models\AcademicYear::where('is_active', true)->first() ?? 'Academic Session';

        $enrollments = \App\Models\Enrollment::where('college_id', $collegeId)
            ->where(function ($q) use ($academicYearId) {
                if ($academicYearId && strlen((string)$academicYearId) > 10) {
                    $q->where('academic_year_id', $academicYearId);
                }
            })
            ->with('user')
            ->get();

        $students = $enrollments->map(fn ($e) => [
            'roll_number' => $e->roll_number ?? 'Pending',
            'name' => $e->user->full_name,
            'father_name' => $e->father_name ?? ($e->user->father_name ?? 'N/A'),
            'cnic' => $e->user->cnic,
            'gender' => $e->gender,
            'program' => $e->program,
        ])->toArray();

        $pdf = $pdfService->generateCollegeCompleteListPdf($college, $academicYear, $students);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "inline; filename=\"complete-list-{$college->code}.pdf\"",
        ]);
    }
}
