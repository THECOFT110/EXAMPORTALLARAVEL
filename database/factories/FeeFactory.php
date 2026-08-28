<?php

namespace Database\Factories;

use App\Models\Enrollment;
use App\Models\Fee;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class FeeFactory extends Factory
{
    protected $model = Fee::class;

    public function definition(): array
    {
        return [
            'id' => (string) Str::uuid(),
            'enrollment_id' => Enrollment::factory(),
            'challan_number' => Fee::generateChallanNumber(),
            'amount' => 1500.00,
            'status' => 'UNPAID',
            'due_date' => now()->addDays(7),
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'PAID',
            'paid_at' => now(),
            'payment_method' => 'ONLINE',
            'transaction_id' => 'TXN-' . strtoupper(Str::random(10)),
        ]);
    }

    public function pendingVerification(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'PENDING_VERIFICATION',
            'paid_at' => now(),
            'payment_method' => 'JazzCash',
            'transaction_id' => 'JC-' . fake()->numberBetween(10000000, 99999999),
        ]);
    }

    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'VERIFIED',
            'paid_at' => now(),
            'payment_method' => 'BANK',
            'transaction_id' => 'HBL-' . fake()->numberBetween(10000000, 99999999),
        ]);
    }
}
