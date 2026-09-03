<?php

namespace App\Http\Controllers;

use App\Services\GoogleVisionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OcrController extends Controller
{
    protected GoogleVisionService $visionService;

    public function __construct(GoogleVisionService $visionService)
    {
        $this->visionService = $visionService;
    }

    /**
     * Scan an uploaded document using Google Cloud Vision OCR
     */
    public function scanDocument(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'file' => 'required|file|mimes:jpeg,jpg,png,webp,pdf|max:5120',
            'doc_type' => 'required|string|in:cnic,matric,inter,document',
            'target_cnic' => 'nullable|string|max:25',
            'target_name' => 'nullable|string|max:100',
            'target_roll' => 'nullable|string|max:50',
        ]);

        $file = $request->file('file');
        $docType = $validated['doc_type'];

        // If file is PDF, handle as attached PDF
        if ($file->getMimeType() === 'application/pdf') {
            return response()->json([
                'success' => true,
                'provider' => 'google_cloud_vision',
                'is_pdf' => true,
                'match_type' => 'PDF_ATTACHED',
                'match_reason' => 'PDF Document Attached',
                'confidence' => 1.0,
                'badge_class' => 'bg-primary-subtle text-primary border border-primary',
                'badge_icon' => 'fas fa-file-pdf',
                'detected_data' => [
                    'doc_type' => $docType,
                    'is_pdf' => true,
                ],
            ]);
        }

        // Perform Google Cloud Vision Scan
        $scanResult = $this->visionService->scanDocument($file, $docType);

        $user = $request->user();
        $targetCnic = $validated['target_cnic'] ?? $user?->cnic;
        $targetName = $validated['target_name'] ?? $user?->full_name;
        $targetRoll = $validated['target_roll'] ?? null;

        $matchAnalysis = $this->visionService->matchWithStudentData(
            $scanResult,
            $targetCnic,
            $targetName,
            $targetRoll,
            $docType
        );

        $badgeClass = 'bg-info-subtle text-dark border';
        $badgeIcon = 'fas fa-file-alt';

        if ($matchAnalysis['is_matched']) {
            $badgeClass = 'bg-success text-white';
            $badgeIcon = 'fas fa-check-circle';
        }

        return response()->json([
            'success' => true,
            'provider' => $matchAnalysis['provider'],
            'is_matched' => $matchAnalysis['is_matched'],
            'match_type' => $matchAnalysis['match_type'],
            'match_reason' => $matchAnalysis['match_reason'],
            'confidence' => $matchAnalysis['confidence'],
            'badge_class' => $badgeClass,
            'badge_icon' => $badgeIcon,
            'detected_data' => $matchAnalysis['detected_data'],
            'is_mock' => $scanResult['is_mock'] ?? false,
        ]);
    }
}
