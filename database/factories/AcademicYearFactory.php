<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AcademicYearFactory extends Factory
{
    protected $model = AcademicYear::class;

    public function definition(): array
    {
        $startYear = fake()->numberBetween(2024, 2028);

        return [
            'id' => (string) Str::uuid(),
            'name' => "{$startYear}-" . ($startYear + 1),
            'start_date' => now()->startOfYear(),
            'end_date' => now()->endOfYear(),
            'is_active' => true,
        ];
    }
}
