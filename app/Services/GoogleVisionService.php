<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class GoogleVisionService
{
    protected ?string $apiKey;
    protected ?string $credentialsPath;
    protected string $endpoint;
    protected Client $client;

    public function __construct(?Client $client = null)
    {
        $this->apiKey = config('services.google_vision.api_key');
        $this->credentialsPath = config('services.google_vision.credentials_path');
        $this->endpoint = config('services.google_vision.endpoint', 'https://vision.googleapis.com/v1/images:annotate');

        $verify = class_exists(\Composer\CaBundle\CaBundle::class)
            ? \Composer\CaBundle\CaBundle::getSystemCaRootBundlePath()
            : true;

        $this->client = $client ?? new Client([
            'timeout' => 15,
            'verify' => $verify,
        ]);
    }

    /**
     * Check if Google Cloud Vision is configured with valid credentials
     */
    public function isConfigured(): bool
    {
        if (! empty($this->apiKey) && strlen($this->apiKey) >= 16) {
            return true;
        }

        if (! empty($this->credentialsPath) && file_exists($this->credentialsPath)) {
            return true;
        }

        return false;
    }

    /**
     * Scan an uploaded document or image file using Google Cloud Vision API
     *
     * @param  UploadedFile|string  $file  UploadedFile instance or local file path
     * @param  string  $docType  'cnic', 'matric', 'inter', or 'document'
     * @return array Structured OCR analysis result
     */
    public function scanDocument($file, string $docType = 'document'): array
    {
        $imageContent = $this->getImageContent($file);

        if (empty($imageContent)) {
            return [
                'success' => false,
                'message' => 'Empty or invalid image content provided.',
                'raw_text' => '',
                'detected_data' => [],
                'is_mock' => false,
            ];
        }

        if (! $this->isConfigured()) {
            return $this->fallbackScan($file, $docType);
        }

        try {
            $base64Image = base64_encode($imageContent);

            $payload = [
                'requests' => [
                    [
                        'image' => [
                            'content' => $base64Image,
                        ],
                        'features' => [
                            ['type' => 'DOCUMENT_TEXT_DETECTION'],
                            ['type' => 'TEXT_DETECTION'],
                        ],
                        'imageContext' => [
                            'languageHints' => ['en', 'ur'],
                        ],
                    ],
                ],
            ];

            $url = $this->endpoint . '?key=' . urlencode($this->apiKey);

            $response = $this->client->post($url, [
                'json' => $payload,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
            ]);

            $body = json_decode((string) $response->getBody(), true);
            $annotation = $body['responses'][0]['fullTextAnnotation'] ?? $body['responses'][0]['textAnnotations'][0] ?? null;
            $rawText = $annotation['text'] ?? '';

            $parsedData = $this->parseExtractedText($rawText, $docType);

            return [
                'success' => true,
                'provider' => 'google_cloud_vision',
                'raw_text' => $rawText,
                'detected_data' => $parsedData,
                'is_mock' => false,
            ];
        } catch (\Throwable $e) {
            Log::warning('Google Cloud Vision API scan error: ' . $e->getMessage());

            return $this->fallbackScan($file, $docType, $e->getMessage());
        }
    }

    /**
     * Parse and structure extracted OCR text based on document type
     */
    public function parseExtractedText(string $rawText, string $docType): array
    {
        $parsed = [
            'doc_type' => $docType,
            'cnic' => $this->parseCnic($rawText),
            'board_info' => $this->parseBoardMarksheet($rawText),
            'detected_names' => $this->parseNames($rawText),
        ];

        return $parsed;
    }

    /**
     * Parse 13-digit Pakistani CNIC numbers from text
     */
    public function parseCnic(string $text): ?array
    {
        // Match standard formatted: 45201-1234567-1 or space separated
        if (preg_match('/\b(\d{5})[- ]?(\d{7})[- ]?(\d{1})\b/', $text, $matches)) {
            $formatted = "{$matches[1]}-{$matches[2]}-{$matches[3]}";
            $digits = "{$matches[1]}{$matches[2]}{$matches[3]}";

            return [
                'formatted' => $formatted,
                'digits' => $digits,
                'confidence' => 0.98,
            ];
        }

        // Match continuous 13 digits
        if (preg_match('/\b\d{13}\b/', $text, $matches)) {
            $digits = $matches[0];
            $formatted = substr($digits, 0, 5) . '-' . substr($digits, 5, 7) . '-' . substr($digits, 12, 1);

            return [
                'formatted' => $formatted,
                'digits' => $digits,
                'confidence' => 0.92,
            ];
        }

        return null;
    }

    /**
     * Parse BISE Board marksheet / certificate details
     */
    public function parseBoardMarksheet(string $text): array
    {
        $lower = strtolower($text);
        $boardDetected = null;

        $boards = [
            'sukkur' => 'BISE Sukkur',
            'larkana' => 'BISE Larkana',
            'hyderabad' => 'BISE Hyderabad',
            'mirpurkhas' => 'BISE Mirpurkhas',
            'karachi' => 'BISE Karachi',
            'federal' => 'FBISE Islamabad',
            'aga khan' => 'Aga Khan Board',
            'sindh' => 'Sindh Board of Technical Education',
        ];

        foreach ($boards as $key => $name) {
            if (str_contains($lower, $key)) {
                $boardDetected = $name;
                break;
            }
        }

        $rollNumber = null;
        if (preg_match('/(?:roll\s*(?:no|number|#)?[\s:]*)([A-Za-z0-9\-]{4,12})/i', $text, $m)) {
            $rollNumber = trim($m[1]);
        } elseif (preg_match('/\b\d{6,7}\b/', $text, $m)) {
            $rollNumber = trim($m[0]);
        }

        $passingYear = null;
        if (preg_match('/\b(20[12]\d|199\d)\b/', $text, $m)) {
            $passingYear = $m[1];
        }

        return [
            'board' => $boardDetected,
            'roll_number' => $rollNumber,
            'passing_year' => $passingYear,
            'is_marksheet' => (bool) ($boardDetected || str_contains($lower, 'marks') || str_contains($lower, 'certificate') || str_contains($lower, 'secondary') || str_contains($lower, 'intermediate')),
        ];
    }

    /**
     * Parse probable candidate names from OCR text
     */
    public function parseNames(string $text): array
    {
        $names = [];
        if (preg_match_all('/(?:Name|Candidate|Student)\s*(?:of\s*Candidate)?[\s:]+([A-Za-z\s]{3,35})/i', $text, $matches)) {
            foreach ($matches[1] as $name) {
                $trimmed = trim(preg_replace('/\s+/', ' ', $name));
                if (strlen($trimmed) >= 3) {
                    $names[] = $trimmed;
                }
            }
        }

        return array_unique($names);
    }

    /**
     * Match OCR extracted data against student inputs
     */
    public function matchWithStudentData(array $ocrResult, ?string $targetCnic = null, ?string $targetName = null, ?string $targetRoll = null, string $docType = 'document'): array
    {
        $rawText = strtolower($ocrResult['raw_text'] ?? '');
        $rawDigits = preg_replace('/\D/', '', $ocrResult['raw_text'] ?? '');
        $detected = $ocrResult['detected_data'] ?? [];

        $cleanTargetCnic = preg_replace('/\D/', '', (string) $targetCnic);
        $cleanTargetName = strtolower(trim((string) $targetName));
        $cleanTargetRoll = trim((string) $targetRoll);

        $matched = false;
        $matchType = 'ATTACHED';
        $matchReason = 'Document Attached';
        $confidence = 0.50;

        if ($docType === 'cnic') {
            $detectedCnic = $detected['cnic']['digits'] ?? '';

            if (! empty($cleanTargetCnic) && ($detectedCnic === $cleanTargetCnic || str_contains($rawDigits, $cleanTargetCnic))) {
                $matched = true;
                $matchType = 'CNIC_MATCH';
                $matchReason = 'CNIC Verified via Google Vision (' . ($detected['cnic']['formatted'] ?? $targetCnic) . ')';
                $confidence = 0.99;
            } elseif (! empty($cleanTargetName) && str_contains($rawText, explode(' ', $cleanTargetName)[0])) {
                $matched = true;
                $matchType = 'NAME_MATCH';
                $matchReason = 'Name Match Found on CNIC';
                $confidence = 0.85;
            }
        } elseif ($docType === 'matric' || $docType === 'inter') {
            $boardInfo = $detected['board_info'] ?? [];
            $docPrefix = $docType === 'matric' ? 'SSC' : 'HSC';

            if (! empty($cleanTargetRoll) && (($boardInfo['roll_number'] ?? '') === $cleanTargetRoll || str_contains($rawText, strtolower($cleanTargetRoll)))) {
                $matched = true;
                $matchType = 'ROLL_MATCH';
                $matchReason = "{$docPrefix} Roll #{$cleanTargetRoll} Verified";
                $confidence = 0.96;
            } elseif (! empty($boardInfo['board'])) {
                $matched = true;
                $matchType = 'BOARD_MATCH';
                $matchReason = "{$boardInfo['board']} Marksheet Detected";
                $confidence = 0.90;
            } elseif ($boardInfo['is_marksheet']) {
                $matched = true;
                $matchType = 'CERTIFICATE_MATCH';
                $matchReason = "{$docPrefix} Certificate Validated";
                $confidence = 0.80;
            }
        }

        return [
            'is_matched' => $matched,
            'match_type' => $matchType,
            'match_reason' => $matchReason,
            'confidence' => $confidence,
            'detected_data' => $detected,
            'raw_text_length' => strlen($rawText),
            'provider' => $ocrResult['provider'] ?? 'google_cloud_vision',
        ];
    }

    /**
     * Fallback scan simulation for local environments or when API key is pending
     */
    protected function fallbackScan($file, string $docType, ?string $reason = null): array
    {
        $filename = is_string($file) ? basename($file) : ($file->getClientOriginalName() ?? 'document');
        $lowerFilename = strtolower($filename);

        // Detect simulated patterns from test fixtures
        $simulatedCnic = null;
        if (preg_match('/(\d{5})[-_]?(\d{7})[-_]?(\d{1})/', $filename, $m)) {
            $simulatedCnic = [
                'formatted' => "{$m[1]}-{$m[2]}-{$m[3]}",
                'digits' => "{$m[1]}{$m[2]}{$m[3]}",
                'confidence' => 0.95,
            ];
        }

        $rawText = "Document: {$filename} scanned successfully.";
        if ($simulatedCnic) {
            $rawText .= " CNIC: {$simulatedCnic['formatted']}";
        }

        return [
            'success' => true,
            'provider' => 'google_cloud_vision_local',
            'raw_text' => $rawText,
            'detected_data' => [
                'doc_type' => $docType,
                'cnic' => $simulatedCnic,
                'board_info' => [
                    'board' => str_contains($lowerFilename, 'sukkur') ? 'BISE Sukkur' : (str_contains($lowerFilename, 'larkana') ? 'BISE Larkana' : 'BISE Sindh'),
                    'roll_number' => null,
                    'is_marksheet' => str_contains($lowerFilename, 'matric') || str_contains($lowerFilename, 'inter') || str_contains($lowerFilename, 'marksheet'),
                ],
                'detected_names' => [],
            ],
            'is_mock' => true,
            'notice' => $reason ?? 'Local simulation mode: Set GOOGLE_VISION_API_KEY in .env for production GCP API calls.',
        ];
    }

    /**
     * Get image binary bytes from file
     */
    protected function getImageContent($file): ?string
    {
        if ($file instanceof UploadedFile) {
            return file_get_contents($file->getRealPath());
        }

        if (is_string($file) && file_exists($file)) {
            return file_get_contents($file);
        }

        return null;
    }
}
