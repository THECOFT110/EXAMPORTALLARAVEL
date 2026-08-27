<?php

namespace App\Jobs;

use App\Models\Enrollment;
use App\Services\EmailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendEnrollmentNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $enrollment;

    public $type;

    /**
     * Create a new job instance.
     */
    public function __construct(Enrollment $enrollment, string $type = 'approved')
    {
        $this->enrollment = $enrollment;
        $this->type = $type;
    }

    /**
     * Execute the job.
     */
    public function handle(EmailService $emailService): void
    {
        $user = $this->enrollment->user;

        if ($this->type === 'approved') {
            $emailService->sendEnrollmentConfirmation(
                $user->email,
                $user->full_name,
                $this->enrollment->id
            );
        } elseif ($this->type === 'rejected') {
            $emailService->sendEnrollmentRejection(
                $user->email,
                $user->full_name,
                $this->enrollment->rejection_reason ?? 'Not specified'
            );
        }
    }
}
