<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Enrollment;
use App\Models\Fee;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use DatabaseTransactions;

    protected User $student;
    protected User $admin;
    protected Fee $fee;

    protected function setUp(): void
    {
        parent::setUp();

        $this->student = User::where('role', 'STUDENT')->first() ?? User::create([
            'full_name' => 'Payment Test Student',
            'father_name' => 'Father',
            'cnic' => '42101-5544332-1',
            'email' => 'paymentstudent@example.com',
            'password' => bcrypt('password123'),
            'role' => 'STUDENT',
            'is_verified' => true,
        ]);

        $this->admin = User::where('role', 'ADMIN')->first() ?? User::create([
            'full_name' => 'Payment Test Admin',
            'father_name' => 'Admin Father',
            'cnic' => '42101-5544332-2',
            'email' => 'paymentadmin@example.com',
            'password' => bcrypt('password123'),
            'role' => 'ADMIN',
            'is_verified' => true,
        ]);

        $academicYear = AcademicYear::first() ?? AcademicYear::create([
            'name' => '2026-2027',
            'start_date' => now()->startOfYear(),
            'end_date' => now()->endOfYear(),
            'is_active' => true,
        ]);

        $enrollment = Enrollment::create([
            'user_id' => $this->student->id,
            'academic_year_id' => $academicYear->id,
            'program' => 'BBA',
            'session' => '2026-2030',
            'semester' => '1',
            'father_name' => 'Father',
            'dob' => '2004-02-02',
            'gender' => 'FEMALE',
            'address' => 'Khairpur',
            'status' => 'PENDING',
        ]);

        $this->fee = Fee::create([
            'enrollment_id' => $enrollment->id,
            'challan_number' => Fee::generateChallanNumber(),
            'amount' => 1500.00,
            'status' => 'UNPAID',
            'due_date' => now()->addDays(7),
        ]);
    }

    public function test_student_can_fetch_fee_details(): void
    {
        $this->actingAs($this->student, 'sanctum');

        $response = $this->getJson("/api/payment/fees/{$this->fee->id}");
        $response->assertStatus(200)
            ->assertJson([
                'id' => $this->fee->id,
                'challan_number' => $this->fee->challan_number,
                'amount' => '1500.00',
                'status' => 'UNPAID',
            ]);
    }

    public function test_admin_can_mark_and_verify_fee(): void
    {
        $this->actingAs($this->admin, 'sanctum');

        $markResponse = $this->postJson("/api/admin/fees/{$this->fee->id}/mark-paid", [
            'payment_method' => 'BANK',
            'transaction_id' => 'HBL-DEP-123456',
        ]);

        $markResponse->assertStatus(200);

        $verifyResponse = $this->postJson("/api/admin/fees/{$this->fee->id}/verify");
        $verifyResponse->assertStatus(200);

        $this->fee->refresh();
        $this->assertEquals('VERIFIED', $this->fee->status);
    }

    public function test_student_submitting_payment_sets_pending_verification(): void
    {
        $this->actingAs($this->student, 'web');

        $response = $this->post(route('payment.submit', $this->fee->id), [
            'transaction_id' => 'JC-9988776655',
            'payment_method' => 'JazzCash',
        ]);

        $response->assertRedirect(route('student.dashboard'));
        $this->fee->refresh();
        $this->assertEquals('PENDING_VERIFICATION', $this->fee->status);
        $this->assertEquals('JC-9988776655', $this->fee->transaction_id);
    }

    public function test_admin_can_verify_pending_verification_fee(): void
    {
        $this->fee->markAsPendingVerification('EasyPaisa', 'EP-11223344');
        $this->assertEquals('PENDING_VERIFICATION', $this->fee->status);

        $this->actingAs($this->admin, 'sanctum');

        $verifyResponse = $this->postJson("/api/admin/fees/{$this->fee->id}/verify");
        $verifyResponse->assertStatus(200);

        $this->fee->refresh();
        $this->assertEquals('VERIFIED', $this->fee->status);
    }

    public function test_challan_number_generation_format_and_entropy(): void
    {
        $challan1 = Fee::generateChallanNumber();
        $challan2 = Fee::generateChallanNumber();

        $this->assertStringStartsWith('SALU-' . now()->format('Ymd') . '-', $challan1);
        $this->assertNotEquals($challan1, $challan2);
        $this->assertGreaterThanOrEqual(18, strlen($challan1));
    }

    public function test_payment_gateway_webhook_verifies_and_marks_fee_as_verified(): void
    {
        $salt = 'strong_secure_test_salt_1234567890';
        config(['services.jazzcash.salt' => $salt]);

        $payload = [
            'pp_BillReference' => $this->fee->challan_number,
            'pp_TxnRefNo' => 'JC-WEBHOOK-999888',
            'pp_Amount' => (string) ((int) ($this->fee->amount * 100)),
        ];

        ksort($payload);
        $hashString = $salt . '&' . implode('&', $payload);
        $payload['pp_SecureHash'] = strtoupper(hash_hmac('sha256', $hashString, $salt));

        $response = $this->postJson('/api/payment/webhook/jazzcash', $payload);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->fee->refresh();
        $this->assertEquals('VERIFIED', $this->fee->status);
        $this->assertEquals('JC-WEBHOOK-999888', $this->fee->transaction_id);
    }
}
