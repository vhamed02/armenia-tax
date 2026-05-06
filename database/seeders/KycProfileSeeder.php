<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class KycProfileSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('is_admin', false)->get();

        $employers = [
            'Unibank',
            'Ameriabank',
            'Ucom',
            'Beeline Armenia',
            'ACBA Bank',
            'Evocabank',
            'Ardshinbank',
            'SoftConstruct',
        ];

        $occupations = [
            'Software Engineer',
            'Financial Analyst',
            'Bank Officer',
            'Accountant',
            'Project Manager',
            'Sales Manager',
            'Network Engineer',
            'HR Specialist',
        ];

        $profiles = [
            ['risk_level' => 'low',    'limit_min' => 3000000,  'limit_max' => 6000000],
            ['risk_level' => 'low',    'limit_min' => 3000000,  'limit_max' => 6000000],
            ['risk_level' => 'low',    'limit_min' => 3000000,  'limit_max' => 6000000],
            ['risk_level' => 'low',    'limit_min' => 3000000,  'limit_max' => 6000000],
            ['risk_level' => 'low',    'limit_min' => 3000000,  'limit_max' => 6000000],
            ['risk_level' => 'low',    'limit_min' => 3000000,  'limit_max' => 6000000],
            ['risk_level' => 'low',    'limit_min' => 3000000,  'limit_max' => 6000000],
            ['risk_level' => 'low',    'limit_min' => 3000000,  'limit_max' => 6000000],
            ['risk_level' => 'low',    'limit_min' => 3000000,  'limit_max' => 6000000],
            ['risk_level' => 'low',    'limit_min' => 3000000,  'limit_max' => 6000000],
            ['risk_level' => 'medium', 'limit_min' => 6000001,  'limit_max' => 12000000],
            ['risk_level' => 'medium', 'limit_min' => 6000001,  'limit_max' => 12000000],
            ['risk_level' => 'medium', 'limit_min' => 6000001,  'limit_max' => 12000000],
            ['risk_level' => 'high',   'limit_min' => 12000001, 'limit_max' => 24000000],
            ['risk_level' => 'high',   'limit_min' => 12000001, 'limit_max' => 24000000],
        ];

        foreach ($users as $index => $user) {
            $profile = $profiles[$index];
            $limit   = random_int($profile['limit_min'], $profile['limit_max']);

            $user->kycProfile()->create([
                'status'               => 'verified',
                'annual_income_limit'  => $limit,
                'occupation'           => $occupations[$index % count($occupations)],
                'employer_name'        => $employers[$index % count($employers)],
                'kyc_verified_at'      => Carbon::now()->subMonths(random_int(3, 18)),
                'risk_level'           => $profile['risk_level'],
            ]);
        }
    }
}
