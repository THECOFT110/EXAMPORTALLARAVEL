<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\GoogleVisionService;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class GoogleVisionOcrTest extends TestCase
{
    use DatabaseTransactions;

    protected function createStudent(): User
    {
        $randomCnic = '45201-' . rand(1000000, 9999999) . '-' . rand(1, 9);

        return User::create([
            'full_name' => 'Muhammad Ali',
            'father_name' => 'Ali Ahmed',
            'cnic' => $randomCnic,
            'email' => 'mali.' . rand(1000, 9999) . '.' . time() . '@salu.edu.pk',
            'password' => bcrypt('password123'),
            'role' => 'STUDENT',
            'is_verified' => true,
        ]);
    }

    /**
     * Unauthenticated user cannot access OCR scan endpoint
     */
    public function test_unauthenticated_user_cannot_access_ocr_scan_endpoint(): void
    {
        $file = UploadedFile::fake()->image('cnic_front.jpg');

        $response = $this->postJson('/api/ocr/scan-document', [
            'file' => $file,
            'doc_type' => 'cnic',
        ]);

        $response->assertStatus(401);
    }

    /**
     * Attached PDF files are detected and reported gracefully
     */
    public function test_ocr_scan_endpoint_handles_pdf_gracefully(): void
    {
        $student = $this->createStudent();
        $this->actingAs($student, 'sanctum');

        $pdf = UploadedFile::fake()->create('marksheet.pdf', 200, 'application/pdf');

        $response = $this->postJson('/api/ocr/scan-document', [
            'file' => $pdf,
            'doc_type' => 'matric',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'is_pdf' => true,
                'match_type' => 'PDF_ATTACHED',
            ]);
    }

    /**
     * GoogleVisionService accurately extracts Pakistani CNIC numbers
     */
    public function test_google_vision_service_parses_pakistani_cnic_patterns(): void
    {
        $service = new GoogleVisionService();

        // Standard hyphenated format
        $sampleText1 = "GOVERNMENT OF PAKISTAN NATIONAL IDENTITY CARD Name: Muhammad Ali Father: Ali Ahmed CNIC: 45201-1234567-1 Country: Pakistan";
        $cnic1 = $service->parseCnic($sampleText1);

        $this->assertNotNull($cnic1);
        $this->assertEquals('45201-1234567-1', $cnic1['formatted']);
        $this->assertEquals('4520112345671', $cnic1['digits']);

        // Continuous 13-digit format
        $sampleText2 = "ISLAMIC REPUBLIC OF PAKISTAN 4520112345671 IDENTITY CARD";
        $cnic2 = $service->parseCnic($sampleText2);

        $this->assertNotNull($cnic2);
        $this->assertEquals('45201-1234567-1', $cnic2['formatted']);
        $this->assertEquals('4520112345671', $cnic2['digits']);
    }

    /**
     * GoogleVisionService accurately extracts BISE Sindh Board names & roll numbers
     */
    public function test_google_vision_service_parses_board_names_and_roll_numbers(): void
    {
        $service = new GoogleVisionService();

        $sampleMarksheet = "BOARD OF INTERMEDIATE AND SECONDARY EDUCATION SUKKUR SINDH
            SECONDARY SCHOOL CERTIFICATE EXAMINATION ANNUAL 2024
            ROLL NO: 184920
            CANDIDATE NAME: MUHAMMAD ALI
            GRADE: A-1 (850/1100)";

        $boardInfo = $service->parseBoardMarksheet($sampleMarksheet);

        $this->assertEquals('BISE Sukkur', $boardInfo['board']);
        $this->assertEquals('184920', $boardInfo['roll_number']);
        $this->assertEquals('2024', $boardInfo['passing_year']);
        $this->assertTrue($boardInfo['is_marksheet']);
    }

    /**
     * Matching logic confirms CNIC match when scanned CNIC matches student CNIC
     */
    public function test_match_with_student_data_verifies_cnic(): void
    {
        $student = $this->createStudent();
        $service = new GoogleVisionService();

        $ocrResult = [
            'success' => true,
            'raw_text' => "NAME: {$student->full_name} CNIC: {$student->cnic}",
            'detected_data' => [
                'cnic' => [
                    'formatted' => $student->cnic,
                    'digits' => preg_replace('/\D/', '', $student->cnic),
                ],
            ],
            'provider' => 'google_cloud_vision',
        ];

        $analysis = $service->matchWithStudentData(
            $ocrResult,
            $student->cnic,
            $student->full_name,
            null,
            'cnic'
        );

        $this->assertTrue($analysis['is_matched']);
        $this->assertEquals('CNIC_MATCH', $analysis['match_type']);
        $this->assertStringContainsString('CNIC Verified', $analysis['match_reason']);
    }

    /**
     * Live or Mocked Google Cloud Vision API response parsing
     */
    public function test_google_vision_service_with_guzzle_mock_response(): void
    {
        $mockGcpResponse = [
            'responses' => [
                [
                    'fullTextAnnotation' => [
                        'text' => "NATIONAL IDENTITY CARD\nPAKISTAN\nName: Muhammad Ali\nCNIC: 45201-1234567-1",
                    ],
                ],
            ],
        ];

        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], json_encode($mockGcpResponse)),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        config(['services.google_vision.api_key' => 'AIzaSyFakeGoogleVisionApiKey123456789']);

        $service = new GoogleVisionService($client);
        $this->assertTrue($service->isConfigured());

        $fakeFile = UploadedFile::fake()->image('my_cnic.jpg');
        $result = $service->scanDocument($fakeFile, 'cnic');

        $this->assertTrue($result['success']);
        $this->assertEquals('google_cloud_vision', $result['provider']);
        $this->assertEquals('45201-1234567-1', $result['detected_data']['cnic']['formatted']);
    }

    /**
     * Authenticated student can call scan-document API endpoint
     */
    public function test_authenticated_student_can_scan_document_via_api(): void
    {
        $student = $this->createStudent();
        $this->actingAs($student, 'sanctum');

        $file = UploadedFile::fake()->image('my_cnic.jpg');

        $response = $this->postJson('/api/ocr/scan-document', [
            'file' => $file,
            'doc_type' => 'cnic',
            'target_cnic' => $student->cnic,
            'target_name' => $student->full_name,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'provider',
                'is_matched',
                'match_type',
                'match_reason',
                'confidence',
                'badge_class',
                'badge_icon',
            ]);
    }
}
