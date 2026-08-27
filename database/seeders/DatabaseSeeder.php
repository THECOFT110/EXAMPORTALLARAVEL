<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CollegeSeeder::class,
            AcademicYearSeeder::class,
            UserSeeder::class,
            EnrollmentSeeder::class,
            FeeSeeder::class,
            SeatAndAdmitCardSeeder::class,
            ResultSeeder::class,
            SystemSettingsSeeder::class,
        ]);
    }
}
