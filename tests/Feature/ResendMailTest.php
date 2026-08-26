<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ResendMailTest extends TestCase
{
    public function test_resend_mailer_driver_is_configured(): void
    {
        $this->assertArrayHasKey('resend', config('mail.mailers'));
        $this->assertEquals('resend', config('mail.mailers.resend.transport'));
        $this->assertNotEmpty(config('resend.api_key'));
    }

    public function test_email_service_can_send_verification_email(): void
    {
        config(['mail.default' => 'array']);

        $service = new \App\Services\EmailService();
        $sent = $service->sendVerificationEmail('student@example.com', 'Test Student', '654321');

        $this->assertTrue($sent);
    }
}
