<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        $cnic = sprintf('%05d-%07d-%01d', fake()->numberBetween(40000, 49999), fake()->numberBetween(1000000, 9999999), fake()->numberBetween(1, 9));

        return [
            'id' => (string) Str::uuid(),
            'full_name' => fake()->name(),
            'father_name' => fake()->name('male'),
            'cnic' => $cnic,
            'email' => fake()->unique()->safeEmail(),
            'phone' => '03' . fake()->numberBetween(10, 49) . '-' . fake()->numberBetween(1000000, 9999999),
            'password' => 'password123',
            'role' => 'STUDENT',
            'is_verified' => true,
            'college_id' => null,
        ];
    }

    public function student(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'STUDENT',
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'ADMIN',
        ]);
    }

    public function superadmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'SUPERADMIN',
        ]);
    }

    public function collegeAdmin(?string $collegeId = null): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'COLLEGE_ADMIN',
            'college_id' => $collegeId,
        ]);
    }
}
