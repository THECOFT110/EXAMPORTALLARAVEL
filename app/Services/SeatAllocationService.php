<?php

namespace App\Services;

use App\Models\AdmitCard;
use App\Models\College;
use App\Models\Enrollment;
use App\Models\Seat;
use Illuminate\Support\Facades\DB;

class SeatAllocationService
{
    /**
     * Allocate seats to approved enrollments
     */
    public function allocateSeats(string $academicYearId, ?string $collegeId = null): array
    {
        $query = Enrollment::where('academic_year_id', $academicYearId)
            ->where('status', 'APPROVED')
            ->whereDoesntHave('seat');

        if ($collegeId) {
            $query->where('college_id', $collegeId);
        }

        $enrollments = $query->with('college')->get();

        $allocated = 0;
        $maleAllocated = 0;
        $femaleAllocated = 0;
        $colleges = [];

        DB::beginTransaction();

        try {
            foreach ($enrollments as $enrollment) {
                if (! $enrollment->college) {
                    continue;
                }

                // Check capacity
                if ($enrollment->gender === 'MALE') {
                    $available = $enrollment->college->getAvailableBoysCapacity($academicYearId);
                    if ($available <= 0) {
                        continue;
                    }
                } else {
                    $available = $enrollment->college->getAvailableGirlsCapacity($academicYearId);
                    if ($available <= 0) {
                        continue;
                    }
                }

                // Allocate seat
                $seatNo = $this->generateSeatNumber($enrollment->college, $enrollment->gender);

                $seat = Seat::create([
                    'enrollment_id' => $enrollment->id,
                    'exam_center' => $enrollment->college->name,
                    'room_no' => $this->assignRoom($enrollment->college, $enrollment->gender),
                    'seat_no' => $seatNo,
                    'exam_date' => now()->addMonth(),
                ]);

                // Create admit card
                AdmitCard::create([
                    'enrollment_id' => $enrollment->id,
                    'seat_id' => $seat->id,
                    'exam_date' => $seat->exam_date,
                    'exam_center' => $seat->exam_center,
                    'is_issued' => true,
                    'issued_at' => now(),
                ]);

                $allocated++;

                if ($enrollment->gender === 'MALE') {
                    $maleAllocated++;
                } else {
                    $femaleAllocated++;
                }

                if (! in_array($enrollment->college->name, $colleges)) {
                    $colleges[] = $enrollment->college->name;
                }
            }

            DB::commit();

            return [
                'success' => true,
                'total_processed' => $allocated,
                'male_allocated' => $maleAllocated,
                'female_allocated' => $femaleAllocated,
                'colleges_count' => count($colleges),
                'message' => "Successfully allocated {$allocated} seats across ".count($colleges).' colleges.',
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => 'Seat allocation failed: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Get seat allocation statistics
     */
    public function getSeatAllocationStats(?string $academicYearId = null): array
    {
        $query = Enrollment::where('status', 'APPROVED');

        if ($academicYearId) {
            $query->where('academic_year_id', $academicYearId);
        }

        $total = $query->count();
        $allocated = $query->has('seat')->count();
        $pending = $total - $allocated;

        $maleAllocated = $query->has('seat')->where('gender', 'MALE')->count();
        $femaleAllocated = $query->has('seat')->where('gender', 'FEMALE')->count();

        return [
            'total_approved' => $total,
            'allocated' => $allocated,
            'pending_allocation' => $pending,
            'male_allocated' => $maleAllocated,
            'female_allocated' => $femaleAllocated,
        ];
    }

    /**
     * Generate unique seat number for a college
     */
    private function generateSeatNumber(College $college, string $gender): string
    {
        $prefix = $gender === 'MALE' ? 'M' : 'F';
        $count = Seat::where('exam_center', $college->name)
            ->where('seat_no', 'like', $prefix.'%')
            ->count() + 1;

        return $prefix.'-'.str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Assign room based on gender and capacity
     */
    private function assignRoom(College $college, string $gender): string
    {
        $prefix = $gender === 'MALE' ? 'Boys' : 'Girls';

        // Simple room assignment logic - can be enhanced
        $seatsPerRoom = config('app.seats_per_room', 30);
        $roomNumber = floor($count / $seatsPerRoom) + 1;

        return $prefix.' Room '.$roomNumber;
    }

    /**
     * Deallocate seat (in case of cancellation)
     */
    public function deallocateSeat(string $enrollmentId): bool
    {
        $seat = Seat::where('enrollment_id', $enrollmentId)->first();

        if ($seat) {
            AdmitCard::where('enrollment_id', $enrollmentId)->delete();
            $seat->delete();

            return true;
        }

        return false;
    }
}
