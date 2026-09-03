<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use DatabaseTransactions;

    public function test_health_check_returns_ok(): void
    {
        $response = $this->getJson('/api/health');
        $response->assertStatus(200)
            ->assertJson(['status' => 'ok']);
    }

    public function test_email_availability_check(): void
    {
        $response = $this->getJson('/api/auth/check-email?email=unique.new.student@salu.edu.pk');
        $response->assertStatus(200)
            ->assertJson(['available' => true]);
    }

    public function test_student_registration_and_login_with_sanctum_token(): void
    {
        $cnic = '42101-' . rand(1000000, 9999999) . '-1';
        $email = 'teststudent' . rand(1000, 9999) . '@salu.edu.pk';

        $phone = '0300-' . rand(1000000, 9999999);

        $registerResponse = $this->postJson('/api/auth/register', [
            'full_name' => 'Automated Test Student',
            'father_name' => 'Father of Student',
            'cnic' => $cnic,
            'email' => $email,
            'phone' => $phone,
            'password' => 'Password123!',
        ]);

        $registerResponse->assertStatus(201);

        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => $email,
            'password' => 'Password123!',
        ]);

        $loginResponse->assertStatus(200)
            ->assertJsonStructure([
                'token',
                'user' => ['id', 'full_name', 'email', 'role']
            ]);

        $token = $loginResponse->json('token');

        // Test protected me endpoint
        $meResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/auth/me');

        $meResponse->assertStatus(200)
            ->assertJson([
                'email' => strtolower($email),
                'role' => 'STUDENT',
            ]);
    }

    public function test_unauthenticated_user_cannot_access_student_dashboard(): void
    {
        $response = $this->getJson('/api/student/dashboard');
        $response->assertStatus(401);
    }

    private function createTestUser(string $role, string $password = 'Secret@123', bool $mustChangePassword = false): User
    {
        return User::create([
            'full_name' => "Test {$role} User",
            'father_name' => 'Test Father',
            'cnic' => '42101-' . rand(1000000, 9999999) . '-' . rand(1, 9),
            'email' => strtolower($role) . '_' . uniqid() . '@salu.edu.pk',
            'password' => Hash::make($password),
            'role' => $role,
            'is_verified' => true,
            'must_change_password' => $mustChangePassword,
        ]);
    }

    public function test_single_login_board_auto_redirects_student_to_student_dashboard(): void
    {
        $student = $this->createTestUser('STUDENT', 'Student@123');

        $response = $this->post('/login', [
            'email' => $student->email,
            'password' => 'Student@123',
        ]);

        $response->assertRedirect(route('student.dashboard'));
        $this->assertAuthenticatedAs($student);
    }

    public function test_single_login_board_auto_redirects_admin_to_admin_dashboard(): void
    {
        $admin = $this->createTestUser('ADMIN', 'Admin@123');

        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'Admin@123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_single_login_board_auto_redirects_superadmin_to_force_change_password(): void
    {
        $superadmin = $this->createTestUser('SUPERADMIN', 'Admin@12345', true);

        $response = $this->post('/login', [
            'email' => $superadmin->email,
            'password' => 'Admin@12345',
        ]);

        $response->assertRedirect(route('password.force_change'));
        $this->assertAuthenticatedAs($superadmin);
    }

    public function test_superadmin_blocked_from_dashboard_until_password_is_changed(): void
    {
        $superadmin = $this->createTestUser('SUPERADMIN', 'Admin@12345', true);

        $response = $this->actingAs($superadmin, 'web')->get(route('admin.superadmin-dashboard'));
        $response->assertRedirect(route('password.force_change'));
    }

    public function test_superadmin_can_successfully_change_password_on_first_login(): void
    {
        $superadmin = $this->createTestUser('SUPERADMIN', 'Admin@12345', true);

        $response = $this->actingAs($superadmin, 'web')->post(route('password.force_change.update'), [
            'current_password' => 'Admin@12345',
            'password' => 'NewSecureAdmin!2026',
            'password_confirmation' => 'NewSecureAdmin!2026',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $superadmin->refresh();
        $this->assertFalse($superadmin->must_change_password);
        $this->assertNotNull($superadmin->password_changed_at);
        $this->assertTrue(Hash::check('NewSecureAdmin!2026', $superadmin->password));
    }

    public function test_admin_can_render_admin_dashboard_view(): void
    {
        $admin = $this->createTestUser('ADMIN', 'Admin@123', false);

        $response = $this->actingAs($admin, 'web')->get(route('admin.dashboard'));
        $response->assertStatus(200)
            ->assertSee('Admin Dashboard');
    }

    public function test_superadmin_can_render_superadmin_dashboard_view(): void
    {
        $superadmin = $this->createTestUser('SUPERADMIN', 'Admin@123', false);

        $response = $this->actingAs($superadmin, 'web')->get(route('admin.superadmin-dashboard'));
        $response->assertStatus(200)
            ->assertSee('SuperAdmin Control Center');
    }

    public function test_college_admin_can_render_admin_dashboard_view(): void
    {
        $collegeAdmin = $this->createTestUser('COLLEGE_ADMIN', 'Admin@123', false);

        $response = $this->actingAs($collegeAdmin, 'web')->get(route('admin.dashboard'));
        $response->assertStatus(200)
            ->assertSee('College Operations Center');
    }
}
