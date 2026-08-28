<?php

namespace Database\Factories;

use App\Models\College;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CollegeFactory extends Factory
{
    protected $model = College::class;

    public function definition(): array
    {
        $name = 'Government Degree College ' . fake()->city();

        return [
            'id' => (string) Str::uuid(),
            'name' => $name,
            'code' => 'COL-' . strtoupper(Str::random(5)),
            'city' => fake()->city(),
            'district' => 'Khairpur',
            'province' => 'Sindh',
            'type' => fake()->randomElement(['COED', 'BOYS', 'GIRLS']),
            'boys_capacity' => 500,
            'girls_capacity' => 500,
            'address' => fake()->address(),
            'phone' => '0243-' . fake()->numberBetween(500000, 999999),
            'email' => fake()->unique()->safeEmail(),
            'is_active' => true,
        ];
    }
}
