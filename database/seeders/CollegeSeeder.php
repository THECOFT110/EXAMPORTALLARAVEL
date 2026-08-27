<?php

namespace Database\Seeders;

use App\Models\College;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CollegeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvPath = base_path('college-program-list.csv');
        if (!file_exists($csvPath)) {
            $this->command->warn('college-program-list.csv not found, skipping seeder.');
            return;
        }

        $file = fopen($csvPath, 'r');
        $header = fgetcsv($file); // District, College, Programs

        $index = 1;
        while (($row = fgetcsv($file)) !== false) {
            if (count($row) < 3) continue;

            $district = trim($row[0]);
            $collegeName = trim($row[1]);
            $programs = trim($row[2]);

            if (empty($collegeName)) continue;

            // Generate unique code
            $code = 'SALU-COL-' . str_pad($index, 3, '0', STR_PAD_LEFT);
            $index++;

            // Detect gender type from college name
            $type = 'COED';
            if (stripos($collegeName, 'Girls') !== false || stripos($collegeName, 'Women') !== false) {
                $type = 'GIRLS';
            } elseif (stripos($collegeName, 'Boys') !== false || stripos($collegeName, 'Men') !== false) {
                $type = 'BOYS';
            }

            College::firstOrCreate(
                ['name' => $collegeName],
                [
                    'id' => (string) Str::uuid(),
                    'code' => $code,
                    'city' => $district,
                    'district' => $district,
                    'province' => 'Sindh',
                    'type' => $type,
                    'boys_capacity' => ($type === 'GIRLS') ? 0 : 500,
                    'girls_capacity' => ($type === 'BOYS') ? 0 : 500,
                    'address' => $collegeName . ', District ' . $district . ', Sindh',
                    'phone' => '0243-' . rand(550000, 999999),
                    'email' => strtolower(Str::slug(substr($collegeName, 0, 20))) . '@saluexamportal.edu.pk',
                    'is_active' => true,
                ]
            );
        }

        fclose($file);
        $this->command->info('All colleges from college-program-list.csv seeded successfully!');
    }
}

