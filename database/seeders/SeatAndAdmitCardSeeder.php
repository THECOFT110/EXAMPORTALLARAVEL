<?php

namespace Database\Seeders;

use App\Models\AdmitCard;
use App\Models\Enrollment;
use App\Models\Seat;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SeatAndAdmitCardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $approvedEnrollments = Enrollment::where('status', 'APPROVED')->get();

        $examCenters = [
            'Allama I.I. Kazi Examination Center Hall A, SALU Khairpur',
            'Shah Abdul Latif Central Examination Hall 1, Main Campus',
            'Faculty of Natural Sciences Exam Block B, Khairpur',
            'Government Islamia Arts & Commerce College Exam Center, Sukkur',
        ];

        foreach ($approvedEnrollments as $i => $enrollment) {
            $center = $examCenters[$i % count($examCenters)];
            $roomNo = 'Room ' . (101 + ($i % 8));
            $seatNo = 'Seat-' . (10 + $i);

            // 1. Create Seat
            $seat = Seat::updateOrCreate(
                ['enrollment_id' => $enrollment->id],
                [
                    'id' => (string) Str::uuid(),
                    'exam_center' => $center,
                    'room_no' => $roomNo,
                    'seat_no' => $seatNo,
                    'exam_date' => now()->addDays(12 + ($i % 3)),
                    'exam_time' => '09:00:00',
                ]
            );

            // 2. Create Admit Card
            AdmitCard::updateOrCreate(
                ['enrollment_id' => $enrollment->id],
                [
                    'id' => (string) Str::uuid(),
                    'seat_id' => $seat->id,
                    'exam_date' => $seat->exam_date,
                    'exam_center' => $center,
                    'is_issued' => true,
                    'issued_at' => now()->subDays(2),
                ]
            );
        }

        $this->command->info('Seats and Admit Cards seeded successfully!');
    }
}
