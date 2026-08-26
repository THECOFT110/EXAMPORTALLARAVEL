<?php

namespace Database\Seeders;

use App\Models\College;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mainCampus = College::where('code', 'SALU-DCS')->first();
        $gsscCollege = College::where('code', 'GSSC-KHP')->first();
        $sukkurCollege = College::where('code', 'GIAC-SKR')->first();

        // 1. Super Administrator
        User::updateOrCreate(
            ['email' => 'admin@saluexamportal.edu.pk'],
            [
                'id' => (string) Str::uuid(),
                'full_name' => 'Prof. Dr. Abdul Majeed Mirbahar',
                'father_name' => 'Muhammad Yousuf Mirbahar',
                'cnic' => '45201-1111111-1',
                'phone' => '0300-1111111',
                'password' => 'admin123',
                'role' => 'SUPERADMIN',
                'is_verified' => true,
                'college_id' => null,
            ]
        );

        // 2. Controller of Examinations (Admin)
        User::updateOrCreate(
            ['email' => 'admin2@saluexamportal.edu.pk'],
            [
                'id' => (string) Str::uuid(),
                'full_name' => 'Dr. Ghulam Sarwar Shaikh',
                'father_name' => 'Allah Bux Shaikh',
                'cnic' => '45201-2222222-2',
                'phone' => '0300-2222222',
                'password' => 'admin123',
                'role' => 'ADMIN',
                'is_verified' => true,
                'college_id' => null,
            ]
        );

        // 3. College Admin (Khairpur Degree College)
        User::updateOrCreate(
            ['email' => 'principal.gssc@saluexamportal.edu.pk'],
            [
                'id' => (string) Str::uuid(),
                'full_name' => 'Prof. Imtiaz Ahmed Memon',
                'father_name' => 'Ahmed Khan Memon',
                'cnic' => '45201-3333333-3',
                'phone' => '0300-3333333',
                'password' => 'admin123',
                'role' => 'COLLEGE_ADMIN',
                'is_verified' => true,
                'college_id' => $gsscCollege?->id,
            ]
        );

        // 4. Primary Verified Student (Testing Student)
        User::updateOrCreate(
            ['email' => 'student@example.com'],
            [
                'id' => (string) Str::uuid(),
                'full_name' => 'Ali Raza Kalhoro',
                'father_name' => 'Muhammad Usman Kalhoro',
                'cnic' => '45201-1234567-1',
                'phone' => '0300-1234567',
                'password' => 'student123',
                'role' => 'STUDENT',
                'is_verified' => true,
                'college_id' => $mainCampus?->id,
            ]
        );

        // 5. Additional Realistic Students
        $students = [
            [
                'full_name' => 'Dua Fatima Soomro',
                'father_name' => 'Ghulam Mustafa Soomro',
                'cnic' => '45201-7654321-2',
                'email' => 'dua.fatima@example.com',
                'phone' => '0301-7654321',
                'college_id' => $mainCampus?->id,
            ],
            [
                'full_name' => 'Bilal Ahmed Solangi',
                'father_name' => 'Bashir Ahmed Solangi',
                'cnic' => '45201-9876543-3',
                'email' => 'bilal.solangi@example.com',
                'phone' => '0302-9876543',
                'college_id' => $gsscCollege?->id,
            ],
            [
                'full_name' => 'Zainab Bano Mahar',
                'father_name' => 'Abdul Rasheed Mahar',
                'cnic' => '45504-2345678-4',
                'email' => 'zainab.mahar@example.com',
                'phone' => '0303-2345678',
                'college_id' => $sukkurCollege?->id,
            ],
            [
                'full_name' => 'Hamza Ali Phulpoto',
                'father_name' => 'Nisar Ahmed Phulpoto',
                'cnic' => '45201-3456789-5',
                'email' => 'hamza.phulpoto@example.com',
                'phone' => '0304-3456789',
                'college_id' => $mainCampus?->id,
            ],
            [
                'full_name' => 'Ayesha Siddiqua Abbasi',
                'father_name' => 'Muhammad Siddique Abbasi',
                'cnic' => '45201-4567890-6',
                'email' => 'ayesha.abbasi@example.com',
                'phone' => '0305-4567890',
                'college_id' => $gsscCollege?->id,
            ],
            [
                'full_name' => 'Tariq Mehmood Chandio',
                'father_name' => 'Mehmood Khan Chandio',
                'cnic' => '45504-5678901-7',
                'email' => 'tariq.chandio@example.com',
                'phone' => '0306-5678901',
                'college_id' => $sukkurCollege?->id,
            ],
        ];

        foreach ($students as $stu) {
            User::updateOrCreate(
                ['email' => $stu['email']],
                [
                    'id' => (string) Str::uuid(),
                    'full_name' => $stu['full_name'],
                    'father_name' => $stu['father_name'],
                    'cnic' => $stu['cnic'],
                    'phone' => $stu['phone'],
                    'password' => 'student123',
                    'role' => 'STUDENT',
                    'is_verified' => true,
                    'college_id' => $stu['college_id'],
                ]
            );
        }

        $this->command->info('Users seeded with real SALU student and admin accounts successfully!');
    }
}
