<?php

namespace App\Jobs;

use App\Models\Fee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ExpireUnpaidFeesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $expiredCount = Fee::where('status', 'UNPAID')
            ->where('due_date', '<', now())
            ->update(['status' => 'EXPIRED']);

        Log::info("Expired {$expiredCount} unpaid fees");
    }
}
