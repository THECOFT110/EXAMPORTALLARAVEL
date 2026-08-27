<?php

namespace App\Jobs;

use App\Services\PdfService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class GeneratePdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $type;

    public $data;

    public $outputPath;

    /**
     * Create a new job instance.
     */
    public function __construct(string $type, array $data, string $outputPath)
    {
        $this->type = $type;
        $this->data = $data;
        $this->outputPath = $outputPath;
    }

    /**
     * Execute the job.
     */
    public function handle(PdfService $pdfService): void
    {
        $pdf = null;

        switch ($this->type) {
            case 'challan':
                $pdf = $pdfService->generateChallan(
                    $this->data['fee'],
                    $this->data['enrollment'],
                    $this->data['user']
                );
                break;

            case 'admit_card':
                $pdf = $pdfService->generateAdmitCard(
                    $this->data['admit_card'],
                    $this->data['enrollment'],
                    $this->data['user']
                );
                break;

            case 'result_card':
                $pdf = $pdfService->generateResultCard(
                    $this->data['enrollment'],
                    $this->data['results'],
                    $this->data['user']
                );
                break;
        }

        if ($pdf) {
            Storage::disk('public')->put($this->outputPath, $pdf);
        }
    }
}
