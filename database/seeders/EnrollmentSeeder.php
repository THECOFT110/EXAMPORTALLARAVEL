<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\College;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EnrollmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $academicYear = AcademicYear::where('is_active', true)->first() ?? AcademicYear::latest()->first();
        $mainCampus = College::where('code', 'SALU-DCS')->first();
        $gsscCollege = College::where('code', 'GSSC-KHP')->first();
        $sukkurCollege = College::where('code', 'GIAC-SKR')->first();

        // 1. Primary Student Enrollment (Ali Raza Kalhoro)
        $primaryStudent = User::where('email', 'student@example.com')->first();
        if ($primaryStudent) {
            Enrollment::updateOrCreate(
                ['user_id' => $primaryStudent->id],
                [
                    'id' => (string) Str::uuid(),
                    'academic_year_id' => $academicYear->id,
                    'college_id' => $mainCampus?->id,
                    'program' => 'BS (4 Years) Computer Science',
                    'session' => '2024-2028',
                    'semester' => '3rd Semester',
                    'father_name' => $primaryStudent->father_name,
                    'surname' => 'Kalhoro',
                    'so_do_wo' => 'S/o',
                    'dob' => '2004-05-14',
                    'gender' => 'MALE',
                    'address' => 'House No. 14, Shaheed Benazir Abad Colony, Khairpur Mirs',
                    'city' => 'Khairpur',
                    'contact_number' => $primaryStudent->phone,
                    'postal_address' => 'SALU Hostel Block C, Shah Abdul Latif University Khairpur',
                    'passing_year' => '2023',
                    'division_obtained' => 'A-1 Grade (82.5%)',
                    'name_of_board' => 'BISE Sukkur',
                    'board' => 'BISE Sukkur',
                    'nationality' => 'Pakistani',
                    'religion' => 'Islam',
                    'domicile_province' => 'Sindh',
                    'domicile_district' => 'Khairpur',
                    'roll_number' => 'SALU-2024-CS-0042',
                    'photo_url' => null,
                    'status' => 'APPROVED',
                    'rejection_reason' => null,
                ]
            );
        }

        // 2. Dua Fatima Soomro (Approved BS Information Technology)
        $dua = User::where('email', 'dua.fatima@example.com')->first();
        if ($dua) {
            Enrollment::updateOrCreate(
                ['user_id' => $dua->id],
                [
                    'id' => (string) Str::uuid(),
                    'academic_year_id' => $academicYear->id,
                    'college_id' => $mainCampus?->id,
                    'program' => 'BS (4 Years) Information Technology',
                    'session' => '2024-2028',
                    'semester' => '3rd Semester',
                    'father_name' => $dua->father_name,
                    'surname' => 'Soomro',
                    'so_do_wo' => 'D/o',
                    'dob' => '2005-02-18',
                    'gender' => 'FEMALE',
                    'address' => 'Bungalow 72, Civil Lines, Sukkur',
                    'city' => 'Sukkur',
                    'contact_number' => $dua->phone,
                    'postal_address' => 'Girls Hostel No. 2, SALU Khairpur',
                    'passing_year' => '2023',
                    'division_obtained' => 'A Grade (78.0%)',
                    'name_of_board' => 'BISE Sukkur',
                    'board' => 'BISE Sukkur',
                    'nationality' => 'Pakistani',
                    'religion' => 'Islam',
                    'domicile_province' => 'Sindh',
                    'domicile_district' => 'Sukkur',
                    'roll_number' => 'SALU-2024-IT-0019',
                    'photo_url' => null,
                    'status' => 'APPROVED',
                    'rejection_reason' => null,
                ]
            );
        }

        // 3. Bilal Ahmed Solangi (Pending BBA)
        $bilal = User::where('email', 'bilal.solangi@example.com')->first();
        if ($bilal) {
            Enrollment::updateOrCreate(
                ['user_id' => $bilal->id],
                [
                    'id' => (string) Str::uuid(),
                    'academic_year_id' => $academicYear->id,
                    'college_id' => $gsscCollege?->id,
                    'program' => 'BBA (Hons) Business Administration',
                    'session' => '2024-2028',
                    'semester' => '1st Semester',
                    'father_name' => $bilal->father_name,
                    'surname' => 'Solangi',
                    'so_do_wo' => 'S/o',
                    'dob' => '2004-11-09',
                    'gender' => 'MALE',
                    'address' => 'Near Mall Road, Gambat, Khairpur',
                    'city' => 'Gambat',
                    'contact_number' => $bilal->phone,
                    'postal_address' => 'Gambat, Dist Khairpur',
                    'passing_year' => '2023',
                    'division_obtained' => 'B Grade (64.5%)',
                    'name_of_board' => 'BISE Sukkur',
                    'board' => 'BISE Sukkur',
                    'nationality' => 'Pakistani',
                    'religion' => 'Islam',
                    'domicile_province' => 'Sindh',
                    'domicile_district' => 'Khairpur',
                    'roll_number' => null,
                    'photo_url' => null,
                    'status' => 'PENDING',
                    'rejection_reason' => null,
                ]
            );
        }

        // 4. Zainab Bano Mahar (Approved B.Com)
        $zainab = User::where('email', 'zainab.mahar@example.com')->first();
        if ($zainab) {
            Enrollment::updateOrCreate(
                ['user_id' => $zainab->id],
                [
                    'id' => (string) Str::uuid(),
                    'academic_year_id' => $academicYear->id,
                    'college_id' => $sukkurCollege?->id,
                    'program' => 'Associate Degree in Commerce (ADC)',
                    'session' => '2024-2026',
                    'semester' => '2nd Semester',
                    'father_name' => $zainab->father_name,
                    'surname' => 'Mahar',
                    'so_do_wo' => 'D/o',
                    'dob' => '2003-08-22',
                    'gender' => 'FEMALE',
                    'address' => 'Ghotki Road, Pano Akil, Sukkur',
                    'city' => 'Sukkur',
                    'contact_number' => $zainab->phone,
                    'postal_address' => 'Sukkur',
                    'passing_year' => '2022',
                    'division_obtained' => 'A Grade (74.0%)',
                    'name_of_board' => 'BISE Sukkur',
                    'board' => 'BISE Sukkur',
                    'nationality' => 'Pakistani',
                    'religion' => 'Islam',
                    'domicile_province' => 'Sindh',
                    'domicile_district' => 'Sukkur',
                    'roll_number' => 'SALU-2024-ADC-0081',
                    'photo_url' => null,
                    'status' => 'APPROVED',
                    'rejection_reason' => null,
                ]
            );
        }

        // 5. Hamza Ali Phulpoto (Approved BS English)
        $hamza = User::where('email', 'hamza.phulpoto@example.com')->first();
        if ($hamza) {
            Enrollment::updateOrCreate(
                ['user_id' => $hamza->id],
                [
                    'id' => (string) Str::uuid(),
                    'academic_year_id' => $academicYear->id,
                    'college_id' => $mainCampus?->id,
                    'program' => 'BS (4 Years) English Literature & Linguistics',
                    'session' => '2024-2028',
                    'semester' => '3rd Semester',
                    'father_name' => $hamza->father_name,
                    'surname' => 'Phulpoto',
                    'so_do_wo' => 'S/o',
                    'dob' => '2004-03-30',
                    'gender' => 'MALE',
                    'address' => 'Station Road, Kot Diji, Khairpur',
                    'city' => 'Kot Diji',
                    'contact_number' => $hamza->phone,
                    'postal_address' => 'SALU Main Campus, Khairpur',
                    'passing_year' => '2023',
                    'division_obtained' => 'A Grade (76.2%)',
                    'name_of_board' => 'BISE Sukkur',
                    'board' => 'BISE Sukkur',
                    'nationality' => 'Pakistani',
                    'religion' => 'Islam',
                    'domicile_province' => 'Sindh',
                    'domicile_district' => 'Khairpur',
                    'roll_number' => 'SALU-2024-ENG-0012',
                    'photo_url' => null,
                    'status' => 'APPROVED',
                    'rejection_reason' => null,
                ]
            );
        }

        $this->command->info('Student Enrollments seeded successfully with realistic data!');
    }
}
