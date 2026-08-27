<?php

namespace Database\Seeders;

use App\Models\Enrollment;
use App\Models\Fee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $enrollments = Enrollment::all();

        foreach ($enrollments as $index => $enrollment) {
            // 1. Enrollment Fee Challan (PKR 2,500)
            if ($enrollment->status === 'APPROVED') {
                Fee::updateOrCreate(
                    [
                        'enrollment_id' => $enrollment->id,
                        'challan_number' => 'CH-ENR-' . str_pad($index + 101, 5, '0', STR_PAD_LEFT),
                    ],
                    [
                        'id' => (string) Str::uuid(),
                        'amount' => 2500.00,
                        'status' => 'VERIFIED',
                        'due_date' => now()->subDays(15),
                        'paid_at' => now()->subDays(16),
                        'payment_method' => ($index % 2 === 0) ? 'JazzCash' : 'EasyPaisa',
                        'transaction_id' => 'TXN-' . rand(10000000, 99999999),
                        'notes' => 'Enrollment fee verified by bank reconciliation',
                    ]
                );

                // 2. Examination Fee Challan (PKR 3,500)
                Fee::updateOrCreate(
                    [
                        'enrollment_id' => $enrollment->id,
                        'challan_number' => 'CH-EXM-' . str_pad($index + 201, 5, '0', STR_PAD_LEFT),
                    ],
                    [
                        'id' => (string) Str::uuid(),
                        'amount' => 3500.00,
                        'status' => ($index === 0) ? 'PAID' : 'VERIFIED',
                        'due_date' => now()->addDays(10),
                        'paid_at' => ($index === 0) ? now()->subHours(2) : now()->subDays(2),
                        'payment_method' => 'JazzCash',
                        'transaction_id' => 'JC-' . rand(10000000, 99999999),
                        'notes' => 'Semester Examination Fee 2025',
                    ]
                );
            } elseif ($enrollment->status === 'PENDING') {
                // Pending student with unpaid/under review fee
                Fee::updateOrCreate(
                    [
                        'enrollment_id' => $enrollment->id,
                        'challan_number' => 'CH-ENR-' . str_pad($index + 101, 5, '0', STR_PAD_LEFT),
                    ],
                    [
                        'id' => (string) Str::uuid(),
                        'amount' => 2500.00,
                        'status' => 'UNPAID',
                        'due_date' => now()->addDays(5),
                        'paid_at' => null,
                        'payment_method' => null,
                        'transaction_id' => null,
                        'notes' => 'Awaiting fee submission',
                    ]
                );
            }
        }

        $this->command->info('Fee Challans seeded successfully!');
    }
}
