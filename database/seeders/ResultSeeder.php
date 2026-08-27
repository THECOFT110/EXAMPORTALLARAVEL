<?php

namespace Database\Seeders;

use App\Models\Enrollment;
use App\Models\Result;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ResultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $approvedEnrollments = Enrollment::where('status', 'APPROVED')->get();

        $courseSets = [
            'BS (4 Years) Computer Science' => [
                ['code' => 'CS-301', 'name' => 'Data Structures & Algorithms', 'marks' => 88, 'total' => 100, 'grade' => 'A'],
                ['code' => 'CS-302', 'name' => 'Database Management Systems', 'marks' => 92, 'total' => 100, 'grade' => 'A+'],
                ['code' => 'MATH-201', 'name' => 'Linear Algebra & Differential Equations', 'marks' => 81, 'total' => 100, 'grade' => 'A'],
                ['code' => 'ENG-201', 'name' => 'Technical & Report Writing', 'marks' => 85, 'total' => 100, 'grade' => 'A'],
                ['code' => 'CS-303', 'name' => 'Object Oriented Analysis & Design', 'marks' => 79, 'total' => 100, 'grade' => 'B+'],
            ],
            'BS (4 Years) Information Technology' => [
                ['code' => 'IT-301', 'name' => 'Web Systems & Technologies', 'marks' => 90, 'total' => 100, 'grade' => 'A+'],
                ['code' => 'IT-302', 'name' => 'Computer Networks & Security', 'marks' => 84, 'total' => 100, 'grade' => 'A'],
                ['code' => 'IT-303', 'name' => 'Human Computer Interaction', 'marks' => 88, 'total' => 100, 'grade' => 'A'],
                ['code' => 'MATH-202', 'name' => 'Probability & Statistics', 'marks' => 77, 'total' => 100, 'grade' => 'B+'],
            ],
            'Associate Degree in Commerce (ADC)' => [
                ['code' => 'ADC-101', 'name' => 'Financial Accounting', 'marks' => 86, 'total' => 100, 'grade' => 'A'],
                ['code' => 'ADC-102', 'name' => 'Business Economics', 'marks' => 79, 'total' => 100, 'grade' => 'B+'],
                ['code' => 'ADC-103', 'name' => 'Business Communication', 'marks' => 82, 'total' => 100, 'grade' => 'A'],
                ['code' => 'ADC-104', 'name' => 'Principles of Management', 'marks' => 80, 'total' => 100, 'grade' => 'A'],
            ],
            'BS (4 Years) English Literature & Linguistics' => [
                ['code' => 'ENG-301', 'name' => 'Classical Poetry & Drama', 'marks' => 87, 'total' => 100, 'grade' => 'A'],
                ['code' => 'ENG-302', 'name' => 'Introduction to Linguistics', 'marks' => 83, 'total' => 100, 'grade' => 'A'],
                ['code' => 'ENG-303', 'name' => 'History of English Literature', 'marks' => 85, 'total' => 100, 'grade' => 'A'],
                ['code' => 'ENG-304', 'name' => 'Phonetics & Phonology', 'marks' => 91, 'total' => 100, 'grade' => 'A+'],
            ],
        ];

        foreach ($approvedEnrollments as $enrollment) {
            $courses = $courseSets[$enrollment->program] ?? [
                ['code' => 'GEN-101', 'name' => 'Academic Core Subject I', 'marks' => 85, 'total' => 100, 'grade' => 'A'],
                ['code' => 'GEN-102', 'name' => 'Academic Core Subject II', 'marks' => 80, 'total' => 100, 'grade' => 'A'],
                ['code' => 'ISL-101', 'name' => 'Islamic Studies / Ethics', 'marks' => 90, 'total' => 100, 'grade' => 'A+'],
                ['code' => 'PKS-101', 'name' => 'Pakistan Studies', 'marks' => 88, 'total' => 100, 'grade' => 'A'],
            ];

            foreach ($courses as $course) {
                Result::updateOrCreate(
                    [
                        'enrollment_id' => $enrollment->id,
                        'subject_code' => $course['code'],
                    ],
                    [
                        'id' => (string) Str::uuid(),
                        'subject_name' => $course['name'],
                        'marks' => $course['marks'],
                        'total_marks' => $course['total'],
                        'grade' => $course['grade'],
                        'published_at' => now()->subDays(5),
                    ]
                );
            }
        }

        $this->command->info('Semester Results seeded successfully!');
    }
}
