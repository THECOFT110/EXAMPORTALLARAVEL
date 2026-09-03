<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\AdmitCard;
use App\Models\College;
use App\Models\Enrollment;
use App\Models\Fee;
use App\Models\Seat;
use App\Models\User;
use App\Services\FileUploadService;
use App\Services\PaymentGatewayService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class SecurityAuditTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Issue #1: Demo salt rejection & gateway security
     */
    public function test_demo_salt_is_rejected_and_gateway_reports_unconfigured(): void
    {
        $gateway = new PaymentGatewayService();

        config(['services.jazzcash.salt' => 'salt_demo']);
        $this->assertFalse($gateway->isJazzCashConfigured());

        config(['services.jazzcash.salt' => 'short']);
        $this->assertFalse($gateway->isJazzCashConfigured());

        config(['services.jazzcash.salt' => 'strong_secure_production_grade_salt_key_123456789']);
        $this->assertTrue($gateway->isJazzCashConfigured());
    }

    /**
     * Issue #2: Mock payment endpoint is completely removed
     */
    public function test_mock_payment_endpoint_is_removed_and_returns_404(): void
    {
        $user = User::create([
            'full_name' => 'Mock Test User',
            'father_name' => 'Father',
            'cnic' => '42101-9999999-9',
            'email' => 'mocktest.' . rand(1000, 9999) . '@salu.edu.pk',
            'password' => bcrypt('password123'),
            'role' => 'STUDENT',
            'is_verified' => true,
        ]);

        $response = $this->actingAs($user, 'sanctum')->postJson("/api/payment/fees/any-fee-id-12345/process");
        $this->assertTrue(in_array($response->status(), [404, 405], true), 'Mock payment processing endpoint must not be accessible.');
    }

    /**
     * Issue #3 & #10: CNIC Normalization, Validation, and Model Scopes
     */
    public function test_cnic_normalization_and_invalid_cnic_rejection(): void
    {
        $user = new User();
        $user->cnic = '1234567890123';
        $this->assertEquals('12345-6789012-3', $user->cnic);

        $this->expectException(\InvalidArgumentException::class);
        $user->cnic = '1234567'; // Invalid length
    }

    public function test_scope_where_cnic_digits_finds_user_reliably(): void
    {
        $user = User::create([
            'full_name' => 'CNIC Test Student',
            'father_name' => 'Father',
            'cnic' => '42101-7766554-1',
            'email' => 'cnictest.' . rand(1000, 9999) . '@salu.edu.pk',
            'password' => bcrypt('password123'),
            'role' => 'STUDENT',
            'is_verified' => true,
        ]);

        $found = User::whereCnicDigits('4210177665541')->first();
        $this->assertNotNull($found);
        $this->assertEquals($user->id, $found->id);

        $foundByHyphenated = User::whereCnicDigits('42101-7766554-1')->first();
        $this->assertNotNull($foundByHyphenated);
        $this->assertEquals($user->id, $foundByHyphenated->id);
    }

    /**
     * Issue #4 & #5: Password Reset Token Expiry (15 mins) and High Entropy
     */
    public function test_forgot_password_sets_15_minute_expiry(): void
    {
        $user = User::create([
            'full_name' => 'Reset Test Student',
            'father_name' => 'Father',
            'cnic' => '42101-8877665-1',
            'email' => 'resettest.' . rand(1000, 9999) . '@salu.edu.pk',
            'password' => bcrypt('password123'),
            'role' => 'STUDENT',
            'is_verified' => true,
        ]);

        $response = $this->postJson('/api/auth/forgot-password', [
            'email' => $user->email,
        ]);

        $response->assertStatus(200);

        $user->refresh();
        $this->assertNotNull($user->password_reset_token_hash);
        $this->assertNotNull($user->password_reset_token_expires_at);

        $diffMinutes = now()->diffInMinutes($user->password_reset_token_expires_at, false);
        $this->assertGreaterThanOrEqual(14, $diffMinutes);
        $this->assertLessThanOrEqual(16, $diffMinutes);
    }

    /**
     * Issue #6: File Upload Magic Byte & Extension Validation
     */
    public function test_file_upload_service_rejects_unsafe_executable_files(): void
    {
        $service = new FileUploadService();
        $fakePhpScript = UploadedFile::fake()->create('malicious.php', 100, 'text/x-php');

        $this->expectException(\InvalidArgumentException::class);
        $service->uploadDocument($fakePhpScript, 'test-user-id', 'document');
    }

    /**
     * Issue #8: PDF Download Authorization Checks
     */
    public function test_unauthorized_student_cannot_download_other_student_challan(): void
    {
        $college = College::first() ?? College::create([
            'name' => 'Test College A',
            'code' => 'TCA-' . rand(100, 999),
            'type' => 'COED',
            'boys_capacity' => 100,
            'girls_capacity' => 100,
            'is_active' => true,
        ]);

        $studentA = User::create([
            'full_name' => 'Student Alice',
            'father_name' => 'Father',
            'cnic' => '42101-1111111-1',
            'email' => 'alice.' . rand(1000, 9999) . '@salu.edu.pk',
            'password' => bcrypt('password123'),
            'role' => 'STUDENT',
            'is_verified' => true,
        ]);

        $studentB = User::create([
            'full_name' => 'Student Bob',
            'father_name' => 'Father',
            'cnic' => '42101-2222222-2',
            'email' => 'bob.' . rand(1000, 9999) . '@salu.edu.pk',
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

        $enrollmentA = Enrollment::create([
            'user_id' => $studentA->id,
            'academic_year_id' => $academicYear->id,
            'college_id' => $college->id,
            'program' => 'BS Computer Science',
            'session' => '2026-2030',
            'semester' => '1',
            'father_name' => 'Father',
            'dob' => '2004-01-01',
            'gender' => 'FEMALE',
            'address' => 'Khairpur',
            'status' => 'PENDING',
        ]);

        $feeA = Fee::create([
            'enrollment_id' => $enrollmentA->id,
            'challan_number' => Fee::generateChallanNumber(),
            'amount' => 1500.00,
            'status' => 'UNPAID',
            'due_date' => now()->addDays(7),
        ]);

        // Student B tries to download Student A's challan
        $this->actingAs($studentB);

        $response = $this->get("/enrollment/{$feeA->id}/challan-pdf");
        $response->assertStatus(403);
    }

    /**
     * Issue #12: Challan Number Entropy
     */
    public function test_challan_number_has_high_entropy_and_unpredictable_format(): void
    {
        $c1 = Fee::generateChallanNumber();
        $c2 = Fee::generateChallanNumber();

        $this->assertNotEquals($c1, $c2);
        $this->assertStringStartsWith('SALU-' . now()->format('Ymd') . '-', $c1);
        $this->assertGreaterThanOrEqual(25, strlen($c1));
    }

    /**
     * Issue #25: Health check security
     */
    public function test_health_check_returns_clean_status_without_server_timestamps(): void
    {
        $response = $this->getJson('/api/health');
        $response->assertStatus(200)
            ->assertJson(['status' => 'ok'])
            ->assertJsonMissing(['timestamp']);
    }
}
