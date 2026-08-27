<?php

namespace App\Jobs;

use App\Services\SeatAllocationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessSeatAllocationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $academicYearId;

    public $collegeId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $academicYearId, ?string $collegeId = null)
    {
        $this->academicYearId = $academicYearId;
        $this->collegeId = $collegeId;
    }

    /**
     * Execute the job.
     */
    public function handle(SeatAllocationService $seatAllocationService): void
    {
        Log::info('Starting seat allocation job', [
            'academic_year_id' => $this->academicYearId,
            'college_id' => $this->collegeId,
        ]);

        $result = $seatAllocationService->allocateSeats(
            $this->academicYearId,
            $this->collegeId
        );

        if ($result['success']) {
            Log::info('Seat allocation completed successfully', $result);
        } else {
            Log::error('Seat allocation failed', $result);
        }
    }
}
