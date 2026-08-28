<?php

namespace App\Http\Controllers;

use App\Models\College;
use App\Models\Enrollment;
use App\Models\EnrollmentWindow;
use App\Models\Fee;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    /**
     * Check if enrollment window is open
     */
    public function checkWindow(Request $request)
    {
        $window = EnrollmentWindow::with('academicYear')
            ->where('is_open', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();

        if (! $window) {
            return response()->json([
                'is_open' => false,
                'message' => 'Enrollment window is currently closed.',
            ]);
        }

        return response()->json([
            'is_open' => true,
            'academic_year' => [
                'id' => $window->academicYear->id,
                'name' => $window->academicYear->name,
            ],
            'start_date' => $window->start_date,
            'end_date' => $window->end_date,
        ]);
    }

    /**
     * Get available colleges
     */
    public function getColleges(Request $request)
    {
        $query = College::where('is_active', true);

        if ($request->filled('type')) {
            $query->where('type', strtoupper($request->type));
        }

        if ($request->filled('district')) {
            $query->where('district', $request->district);
        }

        $colleges = $query->orderBy('name')->get()->map(fn ($c) => [
            'id' => $c->id,
            'name' => $c->name,
            'code' => $c->code,
            'city' => $c->city,
            'district' => $c->district,
            'type' => $c->type,
            'boys_capacity' => $c->boys_capacity,
            'girls_capacity' => $c->girls_capacity,
        ]);

        return response()->json($colleges);
    }

    /**
     * Create or update enrollment (draft)
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'academic_year_id' => 'required|uuid|exists:academic_years,id',
            'college_id' => 'nullable|uuid|exists:colleges,id',
            'program' => 'required|string|max:100',
            'session' => 'required|string|max:50',
            'semester' => 'required|string|max:20',
            'father_name' => 'required|string|max:255',
            'surname' => 'nullable|string|max:100',
            'so_do_wo' => 'nullable|string|max:50',
            'dob' => 'required|date',
            'gender' => 'required|in:MALE,FEMALE,OTHER',
            'address' => 'required|string',
            'city' => 'nullable|string|max:100',
            'contact_number' => 'nullable|string|max:20',
            'postal_address' => 'nullable|string',
            'passing_year' => 'nullable|string|max:20',
            'division_obtained' => 'nullable|string|max:50',
            'last_exam_details' => 'nullable|string',
            'name_of_board' => 'nullable|string|max:100',
            'board' => 'nullable|string|max:100',
            'nationality' => 'nullable|string|max:50',
            'religion' => 'nullable|string|max:50',
            'domicile_province' => 'nullable|string|max:50',
            'domicile_district' => 'nullable|string|max:50',
            'photo_url' => 'nullable|string|max:500',
            'academic_records' => 'nullable|json',
            'documents' => 'nullable|json',
        ]);

        $validated['user_id'] = $user->id;
        $validated['status'] = 'DRAFT';

        $enrollment = Enrollment::create($validated);

        return response()->json([
            'message' => 'Enrollment saved as draft.',
            'enrollment_id' => $enrollment->id,
        ], 201);
    }

    /**
     * Update enrollment
     */
    public function update(Request $request, string $id)
    {
        $user = $request->user();

        $enrollment = Enrollment::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if (! in_array($enrollment->status, ['DRAFT', 'PENDING'])) {
            return response()->json([
                'message' => 'Cannot update enrollment in current status.',
            ], 403);
        }

        $validated = $request->validate([
            'college_id' => 'nullable|uuid|exists:colleges,id',
            'program' => 'sometimes|string|max:100',
            'session' => 'sometimes|string|max:50',
            'semester' => 'sometimes|string|max:20',
            'father_name' => 'sometimes|string|max:255',
            'dob' => 'sometimes|date',
            'gender' => 'sometimes|in:MALE,FEMALE,OTHER',
            'address' => 'sometimes|string',
            'city' => 'nullable|string|max:100',
            'contact_number' => 'nullable|string|max:20',
            'passing_year' => 'nullable|string|max:20',
            'division_obtained' => 'nullable|string|max:50',
            'name_of_board' => 'nullable|string|max:100',
            'domicile_province' => 'nullable|string|max:50',
            'domicile_district' => 'nullable|string|max:50',
            'photo_url' => 'nullable|string|max:500',
        ]);

        $enrollment->update($validated);

        return response()->json([
            'message' => 'Enrollment updated successfully.',
            'enrollment' => $enrollment,
        ]);
    }

    /**
     * Submit enrollment for approval
     */
    public function submit(Request $request, string $id)
    {
        $user = $request->user();

        $enrollment = Enrollment::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if ($enrollment->status !== 'DRAFT') {
            return response()->json([
                'message' => 'Enrollment has already been submitted.',
            ], 400);
        }

        // Validate required fields before submission
        if (empty($enrollment->college_id) ||
            empty($enrollment->program) ||
            empty($enrollment->father_name) ||
            empty($enrollment->address)) {
            return response()->json([
                'message' => 'Please complete all required fields before submitting.',
            ], 422);
        }

        $enrollment->status = 'PENDING';
        $enrollment->save();

        // Generate fee challan
        $fee = Fee::create([
            'enrollment_id' => $enrollment->id,
            'challan_number' => Fee::generateChallanNumber(),
            'amount' => config('app.enrollment_fee_amount', 1500),
            'status' => 'UNPAID',
            'due_date' => now()->addDays(config('app.challan_validity_days', 7)),
        ]);

        return response()->json([
            'message' => 'Enrollment submitted successfully. Please pay the fee to proceed.',
            'enrollment_id' => $enrollment->id,
            'fee' => [
                'id' => $fee->id,
                'challan_number' => $fee->challan_number,
                'amount' => $fee->amount,
                'due_date' => $fee->due_date,
            ],
        ]);
    }

    /**
     * Delete enrollment (only drafts)
     */
    public function destroy(Request $request, string $id)
    {
        $user = $request->user();

        $enrollment = Enrollment::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if ($enrollment->status !== 'DRAFT') {
            return response()->json([
                'message' => 'Only draft enrollments can be deleted.',
            ], 403);
        }

        $enrollment->delete();

        return response()->json([
            'message' => 'Enrollment deleted successfully.',
        ]);
    }

    /**
     * Get available programs
     */
    public function getPrograms(Request $request)
    {
        // This would typically come from database or config
        $programs = [
            'BS Computer Science',
            'BS Information Technology',
            'BS Software Engineering',
            'BS Mathematics',
            'BS Physics',
            'BS Chemistry',
            'BS Botany',
            'BS Zoology',
            'BS English',
            'BS Commerce',
            'BBA',
            'BS Economics',
        ];

        return response()->json($programs);
    }

    /**
     * Web Enrollment create wizard view
     */
    public function webCreate()
    {
        $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
        $colleges = \App\Models\College::where('is_active', true)->get();
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

                if (! isset($districtCollegeProgramData[$district])) {
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
    }

    /**
     * Web Enrollment submission handler
     */
    public function webStore(\App\Http\Requests\StoreEnrollmentRequest $request, \App\Services\FileUploadService $fileUploadService)
    {
        $user = $request->user();
        $activeYear = \App\Models\AcademicYear::where('is_active', true)->first() ?? \App\Models\AcademicYear::latest()->first();
        $validated = $request->validated();

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

        $academicRecords = [$matricRecord, $interRecord];

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
    }

    /**
     * Web Enrollment card view
     */
    public function webCard(Request $request)
    {
        $user = $request->user();
        $enrollments = Enrollment::where('user_id', $user->id)->with('college')->get();

        return view('student.enrollments', compact('enrollments'));
    }

    /**
     * Web Enrollment details view
     */
    public function webShow(Request $request, string $id)
    {
        $enrollment = Enrollment::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->with(['user', 'academicYear', 'college', 'fees', 'seat', 'admitCard', 'results'])
            ->firstOrFail();

        return view('student.enrollment-details', compact('id', 'enrollment'));
    }
}
