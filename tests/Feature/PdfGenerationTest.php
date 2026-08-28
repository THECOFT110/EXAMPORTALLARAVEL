<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\AdmitCard;
use App\Models\College;
use App\Models\Enrollment;
use App\Models\Fee;
use App\Models\Seat;
use App\Models\User;
use App\Services\PdfService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PdfGenerationTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;
    protected Enrollment $enrollment;
    protected Fee $fee;
    protected Seat $seat;
    protected AdmitCard $admitCard;
    protected College $college;
    protected PdfService $pdfService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pdfService = new PdfService();

        $this->college = College::first() ?? College::create([
            'name' => 'SALU Model College Khairpur',
            'code' => 'SMC-' . rand(100, 999),
            'type' => 'COED',
            'boys_capacity' => 100,
            'girls_capacity' => 100,
            'is_active' => true,
        ]);

        $this->user = User::where('role', 'STUDENT')->first() ?? User::create([
            'full_name' => 'PDF Test Candidate',
            'father_name' => 'Candidate Father',
            'cnic' => '42101-7788990-1',
            'email' => 'pdftest@example.com',
            'password' => bcrypt('password123'),
            'role' => 'STUDENT',
            'is_verified' => true,
        ]);

        $academicYear = AcademicYear::first() ?? AcademicYear::create([
            'name' => '2026-2027',
            'start_date' => now()->startOfYear(),
            'end_date' => now()->endOfYear(),
            'is_active' => true,
        ]);

        $this->enrollment = Enrollment::create([
            'user_id' => $this->user->id,
            'academic_year_id' => $academicYear->id,
            'college_id' => $this->college->id,
            'program' => 'BS Information Technology',
            'session' => '2026-2030',
            'semester' => '1',
            'father_name' => $this->user->father_name,
            'dob' => '2004-01-01',
            'gender' => 'MALE',
            'address' => 'Khairpur',
            'roll_number' => 'SALU-26-00099',
            'status' => 'APPROVED',
        ]);

        $this->fee = Fee::create([
            'enrollment_id' => $this->enrollment->id,
            'challan_number' => Fee::generateChallanNumber(),
            'amount' => 1500.00,
            'status' => 'UNPAID',
            'due_date' => now()->addDays(7),
        ]);

        $this->seat = Seat::create([
            'enrollment_id' => $this->enrollment->id,
            'exam_center' => $this->college->name,
            'room_no' => 'Boys Room 1',
            'seat_no' => 'M-0001',
            'exam_date' => now()->addMonth(),
        ]);

        $this->admitCard = AdmitCard::create([
            'enrollment_id' => $this->enrollment->id,
            'seat_id' => $this->seat->id,
            'exam_date' => $this->seat->exam_date,
            'exam_center' => $this->seat->exam_center,
            'is_issued' => true,
            'issued_at' => now(),
        ]);
    }

    public function test_can_generate_challan_pdf(): void
    {
        $output = $this->pdfService->generateChallan($this->fee, $this->enrollment, $this->user);
        $this->assertNotEmpty($output);
        $this->assertStringStartsWith('%PDF-', $output);
    }

    public function test_can_generate_admit_card_pdf(): void
    {
        $output = $this->pdfService->generateAdmitCard($this->admitCard, $this->enrollment, $this->user);
        $this->assertNotEmpty($output);
        $this->assertStringStartsWith('%PDF-', $output);
    }

    public function test_can_generate_result_card_pdf(): void
    {
        $results = [
            ['subject_code' => 'CS-101', 'subject_name' => 'Intro to Programming', 'marks' => 85, 'total_marks' => 100, 'grade' => 'A+'],
            ['subject_code' => 'MATH-101', 'subject_name' => 'Calculus I', 'marks' => 78, 'total_marks' => 100, 'grade' => 'A'],
        ];

        $output = $this->pdfService->generateResultCard($this->enrollment, $results, $this->user);
        $this->assertNotEmpty($output);
        $this->assertStringStartsWith('%PDF-', $output);
    }

    public function test_can_generate_application_form_pdf(): void
    {
        $output = $this->pdfService->generateApplicationForm($this->enrollment, $this->user);
        $this->assertNotEmpty($output);
        $this->assertStringStartsWith('%PDF-', $output);
    }

    public function test_can_generate_enrollment_card_pdf(): void
    {
        $output = $this->pdfService->generateEnrollmentCard($this->enrollment, $this->user);
        $this->assertNotEmpty($output);
        $this->assertStringStartsWith('%PDF-', $output);
    }

    public function test_student_can_download_challan_pdf_via_http(): void
    {
        $this->actingAs($this->user);

        $response = $this->get("/enrollment/{$this->fee->id}/challan-pdf");

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    public function test_student_can_download_admit_card_pdf_via_http(): void
    {
        $this->actingAs($this->user);

        $response = $this->get("/enrollment/{$this->enrollment->id}/admit-card-pdf");

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    public function test_student_can_download_application_form_pdf_via_http(): void
    {
        $this->actingAs($this->user);

        $response = $this->get("/enrollment/{$this->enrollment->id}/application-form-pdf");

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    public function test_student_can_download_enrollment_card_pdf_via_http(): void
    {
        $this->actingAs($this->user);

        $response = $this->get("/enrollment/{$this->enrollment->id}/enrollment-card-pdf");

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    public function test_admin_can_download_college_seat_list_pdf(): void
    {
        $admin = User::where('role', 'ADMIN')->first();
        $this->actingAs($admin);

        $academicYear = AcademicYear::first();
        $response = $this->get("/api/colleges/{$this->college->id}/reports/seat-list-pdf?academicYearId={$academicYear->id}&gender=1");

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    public function test_admin_can_download_college_complete_list_pdf(): void
    {
        $admin = User::where('role', 'ADMIN')->first();
        $this->actingAs($admin);

        $academicYear = AcademicYear::first();
        $response = $this->get("/api/colleges/{$this->college->id}/reports/complete-list-pdf?academicYearId={$academicYear->id}");

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }
}
