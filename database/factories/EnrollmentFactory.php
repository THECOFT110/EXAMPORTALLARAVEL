<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\College;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EnrollmentFactory extends Factory
{
    protected $model = Enrollment::class;

    public function definition(): array
    {
        return [
            'id' => (string) Str::uuid(),
            'user_id' => User::factory(),
            'academic_year_id' => AcademicYear::factory(),
            'college_id' => College::factory(),
            'program' => 'BS Computer Science',
            'session' => '2026-2030',
            'semester' => '1',
            'father_name' => fake()->name('male'),
            'surname' => fake()->lastName(),
            'dob' => '2004-05-15',
            'gender' => fake()->randomElement(['MALE', 'FEMALE']),
            'address' => fake()->address(),
            'city' => 'Khairpur',
            'contact_number' => '0300-1234567',
            'status' => 'PENDING',
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'APPROVED',
            'roll_number' => 'SALU-' . fake()->unique()->numberBetween(10000, 99999),
        ]);
    }
}
