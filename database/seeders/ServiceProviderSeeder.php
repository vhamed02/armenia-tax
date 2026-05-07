<?php

namespace Database\Seeders;

use App\Models\ServiceProvider;
use App\Services\TenantService;
use Illuminate\Database\Seeder;

class ServiceProviderSeeder extends Seeder
{
    public function run(TenantService $tenantService): void
    {
        $credentials = $tenantService->generateApiCredentials();

        ServiceProvider::create([
            'name'       => 'SoftConstruct Gaming',
            'slug'       => 'softconstruct',
            'website'    => 'https://vbet.am',
            'status'     => 'active',
            'api_key'    => $credentials['api_key'],
            'api_secret' => $credentials['api_secret'],
        ]);

        $this->command->newLine();
        $this->command->info('=== SoftConstruct Gaming API Credentials ===');
        $this->command->line('API Key:    ' . $credentials['api_key']);
        $this->command->line('API Secret: ' . $credentials['api_secret']);
        $this->command->info('============================================');
        $this->command->newLine();
    }
}
