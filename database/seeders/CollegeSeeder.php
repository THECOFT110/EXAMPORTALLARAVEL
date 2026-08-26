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
        $colleges = [
            // Main Campus & Faculties
            [
                'name' => 'SALU Main Campus - Department of Computer Science',
                'code' => 'SALU-DCS',
                'city' => 'Khairpur',
                'district' => 'Khairpur',
                'province' => 'Sindh',
                'type' => 'COED',
                'boys_capacity' => 600,
                'girls_capacity' => 400,
            ],
            [
                'name' => 'SALU Main Campus - Faculty of Management Sciences',
                'code' => 'SALU-FMS',
                'city' => 'Khairpur',
                'district' => 'Khairpur',
                'province' => 'Sindh',
                'type' => 'COED',
                'boys_capacity' => 500,
                'girls_capacity' => 350,
            ],
            [
                'name' => 'SALU Main Campus - Faculty of Natural Sciences',
                'code' => 'SALU-FNS',
                'city' => 'Khairpur',
                'district' => 'Khairpur',
                'province' => 'Sindh',
                'type' => 'COED',
                'boys_capacity' => 700,
                'girls_capacity' => 500,
            ],
            [
                'name' => 'SALU Main Campus - Faculty of Law',
                'code' => 'SALU-LAW',
                'city' => 'Khairpur',
                'district' => 'Khairpur',
                'province' => 'Sindh',
                'type' => 'COED',
                'boys_capacity' => 300,
                'girls_capacity' => 200,
            ],

            // District Khairpur Affiliated Colleges
            [
                'name' => 'Government Superior Science College Khairpur',
                'code' => 'GSSC-KHP',
                'city' => 'Khairpur',
                'district' => 'Khairpur',
                'province' => 'Sindh',
                'type' => 'BOYS',
                'boys_capacity' => 800,
                'girls_capacity' => 0,
            ],
            [
                'name' => 'Government Mumtaz Degree College Khairpur',
                'code' => 'GMDC-KHP',
                'city' => 'Khairpur',
                'district' => 'Khairpur',
                'province' => 'Sindh',
                'type' => 'BOYS',
                'boys_capacity' => 650,
                'girls_capacity' => 0,
            ],
            [
                'name' => 'Government Girls Degree College Khairpur',
                'code' => 'GGDC-KHP',
                'city' => 'Khairpur',
                'district' => 'Khairpur',
                'province' => 'Sindh',
                'type' => 'GIRLS',
                'boys_capacity' => 0,
                'girls_capacity' => 750,
            ],
            [
                'name' => 'Government Degree College Gambat',
                'code' => 'GDC-GMB',
                'city' => 'Gambat',
                'district' => 'Khairpur',
                'province' => 'Sindh',
                'type' => 'COED',
                'boys_capacity' => 450,
                'girls_capacity' => 300,
            ],
            [
                'name' => 'Government Degree College Kot Diji',
                'code' => 'GDC-KDJ',
                'city' => 'Kot Diji',
                'district' => 'Khairpur',
                'province' => 'Sindh',
                'type' => 'BOYS',
                'boys_capacity' => 400,
                'girls_capacity' => 0,
            ],
            [
                'name' => 'Government Degree College Ranipur',
                'code' => 'GDC-RNP',
                'city' => 'Ranipur',
                'district' => 'Khairpur',
                'province' => 'Sindh',
                'type' => 'COED',
                'boys_capacity' => 350,
                'girls_capacity' => 250,
            ],

            // District Sukkur Affiliated Colleges
            [
                'name' => 'Government Islamia Arts & Commerce College Sukkur',
                'code' => 'GIAC-SKR',
                'city' => 'Sukkur',
                'district' => 'Sukkur',
                'province' => 'Sindh',
                'type' => 'COED',
                'boys_capacity' => 900,
                'girls_capacity' => 400,
            ],
            [
                'name' => 'Government Degree College Sukkur',
                'code' => 'GDC-SKR',
                'city' => 'Sukkur',
                'district' => 'Sukkur',
                'province' => 'Sindh',
                'type' => 'BOYS',
                'boys_capacity' => 700,
                'girls_capacity' => 0,
            ],
            [
                'name' => 'Government Girls Degree College Rohri',
                'code' => 'GGDC-RHR',
                'city' => 'Rohri',
                'district' => 'Sukkur',
                'province' => 'Sindh',
                'type' => 'GIRLS',
                'boys_capacity' => 0,
                'girls_capacity' => 500,
            ],
            [
                'name' => 'Government Degree College Pano Akil',
                'code' => 'GDC-PA',
                'city' => 'Pano Akil',
                'district' => 'Sukkur',
                'province' => 'Sindh',
                'type' => 'BOYS',
                'boys_capacity' => 450,
                'girls_capacity' => 0,
            ],

            // District Ghotki Affiliated Colleges
            [
                'name' => 'Government Degree College Ghotki',
                'code' => 'GDC-GHT',
                'city' => 'Ghotki',
                'district' => 'Ghotki',
                'province' => 'Sindh',
                'type' => 'BOYS',
                'boys_capacity' => 600,
                'girls_capacity' => 0,
            ],
            [
                'name' => 'Government Degree College Mirpur Mathelo',
                'code' => 'GDC-MPM',
                'city' => 'Mirpur Mathelo',
                'district' => 'Ghotki',
                'province' => 'Sindh',
                'type' => 'COED',
                'boys_capacity' => 450,
                'girls_capacity' => 250,
            ],
            [
                'name' => 'Government Degree College Daharki',
                'code' => 'GDC-DHK',
                'city' => 'Daharki',
                'district' => 'Ghotki',
                'province' => 'Sindh',
                'type' => 'COED',
                'boys_capacity' => 400,
                'girls_capacity' => 200,
            ],

            // District Shikarpur Affiliated Colleges
            [
                'name' => 'Government C&S Degree College Shikarpur',
                'code' => 'GCS-SHK',
                'city' => 'Shikarpur',
                'district' => 'Shikarpur',
                'province' => 'Sindh',
                'type' => 'BOYS',
                'boys_capacity' => 850,
                'girls_capacity' => 0,
            ],
            [
                'name' => 'Government Girls Degree College Shikarpur',
                'code' => 'GGDC-SHK',
                'city' => 'Shikarpur',
                'district' => 'Shikarpur',
                'province' => 'Sindh',
                'type' => 'GIRLS',
                'boys_capacity' => 0,
                'girls_capacity' => 600,
            ],

            // District Naushahro Feroze Affiliated Colleges
            [
                'name' => 'Government Degree College Moro',
                'code' => 'GDC-MRO',
                'city' => 'Moro',
                'district' => 'Naushahro Feroze',
                'province' => 'Sindh',
                'type' => 'COED',
                'boys_capacity' => 500,
                'girls_capacity' => 300,
            ],
            [
                'name' => 'Government Degree College Naushahro Feroze',
                'code' => 'GDC-NSF',
                'city' => 'Naushahro Feroze',
                'district' => 'Naushahro Feroze',
                'province' => 'Sindh',
                'type' => 'BOYS',
                'boys_capacity' => 550,
                'girls_capacity' => 0,
            ],
        ];

        foreach ($colleges as $college) {
            College::updateOrCreate(
                ['code' => $college['code']],
                array_merge($college, [
                    'id' => (string) Str::uuid(),
                    'address' => $college['city'] . ', District ' . $college['district'] . ', ' . $college['province'],
                    'phone' => '0243-' . rand(550000, 999999),
                    'email' => strtolower(str_replace(['-', ' '], '', $college['code'])) . '@saluexamportal.edu.pk',
                    'is_active' => true,
                ])
            );
        }

        $this->command->info('Authentic SALU affiliated colleges seeded successfully!');
    }
}
