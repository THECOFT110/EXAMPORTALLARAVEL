<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendTestEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:test {email : The recipient email address} {--subject= : Optional custom subject}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test email to verify Resend or Mail configuration';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $recipient = $this->argument('email');
        $subject = $this->option('subject') ?: 'SALU Exam Portal - Resend Email Alert Test';
        $mailer = config('mail.default');
        $fromAddress = config('mail.from.address');
        $fromName = config('mail.from.name');

        $this->info("Sending test email using mailer: [{$mailer}]");
        $this->line("From: {$fromName} <{$fromAddress}>");
        $this->line("To: {$recipient}");

        try {
            Mail::raw("Hello,\n\nThis is a test email alert sent from the Shah Abdul Latif University (SALU) Examination Portal using the Resend email service.\n\nTime: ".now()->toIso8601String()."\nEnvironment: ".app()->environment()."\nMailer: {$mailer}", function ($message) use ($recipient, $subject, $fromAddress, $fromName) {
                $message->to($recipient)
                    ->subject($subject)
                    ->from($fromAddress, $fromName);
            });

            $this->info("✔ Test email dispatched successfully to [{$recipient}] via [{$mailer}]!");
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("✖ Failed to send email: ".$e->getMessage());
            return self::FAILURE;
        }
    }
}
