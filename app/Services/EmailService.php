<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailService
{
    /**
     * Send email verification code
     */
    public function sendVerificationEmail(string $email, string $name, string $code): bool
    {
        try {
            Mail::send('emails.verification', [
                'name' => $name,
                'code' => $code,
            ], function ($message) use ($email, $name) {
                $message->to($email, $name)
                    ->subject('Email Verification - SALU Exam Portal');
            });

            return true;
        } catch (\Exception $e) {
            Log::error('Email verification failed: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Send password reset email
     */
    public function sendPasswordResetEmail(string $email, string $name, string $resetLink): bool
    {
        try {
            Mail::send('emails.password-reset', [
                'name' => $name,
                'reset_link' => $resetLink,
            ], function ($message) use ($email, $name) {
                $message->to($email, $name)
                    ->subject('Password Reset Request - SALU Exam Portal');
            });

            return true;
        } catch (\Exception $e) {
            Log::error('Password reset email failed: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Send enrollment confirmation email
     */
    public function sendEnrollmentConfirmation(string $email, string $name, string $enrollmentId): bool
    {
        try {
            Mail::send('emails.enrollment-confirmation', [
                'name' => $name,
                'enrollment_id' => $enrollmentId,
                'dashboard_link' => url('/student/dashboard'),
            ], function ($message) use ($email, $name) {
                $message->to($email, $name)
                    ->subject('Enrollment Approved - SALU Exam Portal');
            });

            return true;
        } catch (\Exception $e) {
            Log::error('Enrollment confirmation email failed: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Send enrollment rejection email
     */
    public function sendEnrollmentRejection(string $email, string $name, string $reason): bool
    {
        try {
            Mail::send('emails.enrollment-rejection', [
                'name' => $name,
                'reason' => $reason,
                'contact_link' => url('/contact'),
            ], function ($message) use ($email, $name) {
                $message->to($email, $name)
                    ->subject('Enrollment Status - SALU Exam Portal');
            });

            return true;
        } catch (\Exception $e) {
            Log::error('Enrollment rejection email failed: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Send fee payment confirmation
     */
    public function sendPaymentConfirmation(string $email, string $name, string $challanNumber, float $amount): bool
    {
        try {
            Mail::send('emails.payment-confirmation', [
                'name' => $name,
                'challan_number' => $challanNumber,
                'amount' => $amount,
            ], function ($message) use ($email, $name) {
                $message->to($email, $name)
                    ->subject('Payment Received - SALU Exam Portal');
            });

            return true;
        } catch (\Exception $e) {
            Log::error('Payment confirmation email failed: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Send admit card notification
     */
    public function sendAdmitCardNotification(string $email, string $name, string $rollNumber): bool
    {
        try {
            Mail::send('emails.admit-card-ready', [
                'name' => $name,
                'roll_number' => $rollNumber,
                'download_link' => url('/student/dashboard'),
            ], function ($message) use ($email, $name) {
                $message->to($email, $name)
                    ->subject('Admit Card Available - SALU Exam Portal');
            });

            return true;
        } catch (\Exception $e) {
            Log::error('Admit card notification failed: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Send result published notification
     */
    public function sendResultNotification(string $email, string $name, string $rollNumber): bool
    {
        try {
            Mail::send('emails.result-published', [
                'name' => $name,
                'roll_number' => $rollNumber,
                'result_link' => url('/student/results'),
            ], function ($message) use ($email, $name) {
                $message->to($email, $name)
                    ->subject('Examination Results Published - SALU Exam Portal');
            });

            return true;
        } catch (\Exception $e) {
            Log::error('Result notification failed: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Send bulk email notification
     */
    public function sendBulkEmail(array $recipients, string $subject, string $view, array $data): bool
    {
        try {
            foreach ($recipients as $recipient) {
                Mail::send($view, $data, function ($message) use ($recipient, $subject) {
                    $message->to($recipient['email'], $recipient['name'])
                        ->subject($subject);
                });
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Bulk email failed: '.$e->getMessage());

            return false;
        }
    }
}
