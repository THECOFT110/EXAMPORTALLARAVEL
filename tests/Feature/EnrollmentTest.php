<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\College;
use App\Models\Enrollment;
use App\Models\EnrollmentWindow;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class EnrollmentTest extends TestCase
{
    use DatabaseTransactions;

    protected User $student;
    protected User $admin;
    protected AcademicYear $academicYear;
    protected College $college;

    protected function setUp(): void
    {
        parent::setUp();

        $this->student = User::where('role', 'STUDENT')->first() ?? User::create([
            'full_name' => 'Enrollment Test Student',
            'father_name' => 'Test Father',
            'cnic' => '42101-9988776-1',
            'email' => 'enrollstudent@example.com',
            'password' => bcrypt('password123'),
            'role' => 'STUDENT',
            'is_verified' => true,
        ]);

        $this->admin = User::where('role', 'ADMIN')->first() ?? User::create([
            'full_name' => 'Enrollment Test Admin',
            'father_name' => 'Admin Father',
            'cnic' => '42101-9988776-2',
            'email' => 'enrolladmin@example.com',
            'password' => bcrypt('password123'),
            'role' => 'ADMIN',
            'is_verified' => true,
        ]);

        $this->academicYear = AcademicYear::first() ?? AcademicYear::create([
            'name' => '2026-2027',
            'start_date' => now()->startOfYear(),
            'end_date' => now()->endOfYear(),
            'is_active' => true,
        ]);

        $this->college = College::first() ?? College::create([
            'name' => 'Government Degree College Khairpur',
            'code' => 'GDC-KHP-' . rand(100, 999),
            'type' => 'COED',
            'boys_capacity' => 200,
            'girls_capacity' => 200,
            'is_active' => true,
        ]);
    }

    public function test_student_can_create_and_submit_enrollment(): void
    {
        $this->actingAs($this->student, 'sanctum');

        $storeResponse = $this->postJson('/api/enrollment', [
            'academic_year_id' => $this->academicYear->id,
            'college_id' => $this->college->id,
            'program' => 'BS Computer Science',
            'session' => '2026-2030',
            'semester' => '1',
            'father_name' => 'Test Father',
            'dob' => '2004-05-15',
            'gender' => 'MALE',
            'address' => 'Khairpur Sindh',
            'city' => 'Khairpur',
            'nationality' => 'Pakistani',
        ]);

        $storeResponse->assertStatus(201);
        $enrollmentId = $storeResponse->json('enrollment_id');
        $this->assertNotNull($enrollmentId);

        // Submit enrollment
        $submitResponse = $this->postJson("/api/enrollment/{$enrollmentId}/submit");
        $submitResponse->assertStatus(200)
            ->assertJsonStructure(['message', 'enrollment_id', 'fee' => ['challan_number', 'amount']]);

        // Admin approves enrollment
        $this->actingAs($this->admin, 'sanctum');
        $approveResponse = $this->putJson("/api/admin/enrollments/{$enrollmentId}/approve");
        $approveResponse->assertStatus(200)
            ->assertJsonStructure(['message', 'roll_number']);

        $enrollment = Enrollment::find($enrollmentId);
        $this->assertEquals('APPROVED', $enrollment->status);
        $this->assertNotEmpty($enrollment->roll_number);
    }

    public function test_student_photo_upload_and_optimization(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $file = \Illuminate\Http\UploadedFile::fake()->image('avatar.jpg', 600, 800);
        $service = new \App\Services\FileUploadService();
        $url = $service->uploadStudentPhoto($file, $this->student->id);

        $this->assertNotEmpty($url);
        $this->assertStringContainsString('uploads/students/photos/', $url);
    }
}
