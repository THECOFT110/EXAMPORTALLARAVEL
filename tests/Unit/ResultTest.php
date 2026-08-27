<?php

namespace Tests\Unit;

use App\Models\Result;
use PHPUnit\Framework\TestCase;

class ResultTest extends TestCase
{
    public function test_calculate_grade_returns_correct_grades(): void
    {
        $this->assertEquals('A+', Result::calculateGrade(85, 100));
        $this->assertEquals('A', Result::calculateGrade(75, 100));
        $this->assertEquals('B', Result::calculateGrade(65, 100));
        $this->assertEquals('C', Result::calculateGrade(55, 100));
        $this->assertEquals('D', Result::calculateGrade(45, 100));
        $this->assertEquals('E', Result::calculateGrade(35, 100));
        $this->assertEquals('F', Result::calculateGrade(20, 100));
    }
}
