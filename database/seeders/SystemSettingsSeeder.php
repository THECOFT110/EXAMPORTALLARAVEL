<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class SystemSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            'enrollment_fee_amount' => '1500',
            'exam_fee_amount' => '2000',
            'late_fee_amount' => '500',
            'challan_validity_days' => '7',
            'site_name' => 'SALU Exam Portal',
            'site_email' => 'info@saluexamportal.edu.pk',
            'site_phone' => '022-2771331',
            'site_address' => 'Shah Abdul Latif University, Khairpur, Sindh',
            'allow_enrollment' => 'true',
            'maintenance_mode' => 'false',
        ];

        foreach ($settings as $key => $value) {
            SystemSetting::create([
                'key' => $key,
                'value' => $value,
            ]);
        }

        $this->command->info('System settings seeded successfully!');
    }
}
