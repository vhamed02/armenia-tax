<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name'        => 'ARFIMS Admin',
            'national_id' => '00000001',
            'email'       => 'admin@arfims.am',
            'phone'       => '+37410000000',
            'password'    => Hash::make('admin123'),
            'is_admin'    => true,
        ]);

        $admin->kycProfile()->create([
            'status'              => 'verified',
            'annual_income_limit' => 50000000,
            'occupation'          => 'System Administrator',
            'employer_name'       => 'Armenian Revenue Service',
            'kyc_verified_at'     => Carbon::now()->subYear(),
            'risk_level'          => 'low',
        ]);
    }
}
