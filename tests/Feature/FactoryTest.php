<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\College;
use App\Models\Enrollment;
use App\Models\Fee;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class FactoryTest extends TestCase
{
    use DatabaseTransactions;

    public function test_all_model_factories_create_valid_records(): void
    {
        $student = User::factory()->student()->create();
        $this->assertNotNull($student->id);
        $this->assertEquals('STUDENT', $student->role);

        $college = College::factory()->create();
        $this->assertNotNull($college->id);

        $academicYear = AcademicYear::factory()->create();
        $this->assertNotNull($academicYear->id);

        $enrollment = Enrollment::factory()->create([
            'user_id' => $student->id,
            'college_id' => $college->id,
            'academic_year_id' => $academicYear->id,
        ]);
        $this->assertNotNull($enrollment->id);

        $fee = Fee::factory()->pendingVerification()->create([
            'enrollment_id' => $enrollment->id,
        ]);
        $this->assertNotNull($fee->id);
        $this->assertEquals('PENDING_VERIFICATION', $fee->status);
    }
}
