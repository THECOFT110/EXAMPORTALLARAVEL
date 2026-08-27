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

    public function test_single_login_board_auto_redirects_student_to_student_dashboard(): void
    {
        $student = User::where('role', 'STUDENT')->first();

        $response = $this->post('/login', [
            'email' => $student->email,
            'password' => 'student123',
        ]);

        $response->assertRedirect(route('student.dashboard'));
        $this->assertAuthenticatedAs($student);
    }

    public function test_single_login_board_auto_redirects_admin_to_admin_dashboard(): void
    {
        $admin = User::where('role', 'ADMIN')->first();

        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'admin123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_single_login_board_auto_redirects_superadmin_to_admin_dashboard(): void
    {
        $superadmin = User::where('role', 'SUPERADMIN')->first();

        $response = $this->post('/login', [
            'email' => $superadmin->email,
            'password' => 'admin123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($superadmin);
    }

    public function test_admin_can_render_admin_dashboard_view(): void
    {
        $admin = User::where('role', 'ADMIN')->first();

        $response = $this->actingAs($admin, 'web')->get(route('admin.dashboard'));
        $response->assertStatus(200)
            ->assertSee('Admin Dashboard');
    }

    public function test_superadmin_can_render_superadmin_dashboard_view(): void
    {
        $superadmin = User::where('role', 'SUPERADMIN')->first();

        $response = $this->actingAs($superadmin, 'web')->get(route('admin.superadmin-dashboard'));
        $response->assertStatus(200)
            ->assertSee('SuperAdmin Control Center');
    }
}
