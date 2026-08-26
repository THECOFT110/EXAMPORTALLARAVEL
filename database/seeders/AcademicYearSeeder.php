<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\EnrollmentWindow;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AcademicYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Current Active Academic Year
        $currentYear = AcademicYear::updateOrCreate(
            ['name' => '2024-2025'],
            [
                'id' => (string) Str::uuid(),
                'start_date' => '2024-09-01',
                'end_date' => '2025-08-31',
                'is_active' => true,
            ]
        );

        // Open Enrollment & Examination Window
        EnrollmentWindow::updateOrCreate(
            ['academic_year_id' => $currentYear->id],
            [
                'id' => (string) Str::uuid(),
                'start_date' => now()->subDays(30),
                'end_date' => now()->addMonths(3),
                'is_open' => true,
            ]
        );

        // 2. Next Academic Session
        AcademicYear::updateOrCreate(
            ['name' => '2025-2026'],
            [
                'id' => (string) Str::uuid(),
                'start_date' => '2025-09-01',
                'end_date' => '2026-08-31',
                'is_active' => false,
            ]
        );

        // 3. Previous Academic Session
        AcademicYear::updateOrCreate(
            ['name' => '2023-2024'],
            [
                'id' => (string) Str::uuid(),
                'start_date' => '2023-09-01',
                'end_date' => '2024-08-31',
                'is_active' => false,
            ]
        );

        $this->command->info('SALU Academic Sessions and Windows seeded successfully!');
    }
}
