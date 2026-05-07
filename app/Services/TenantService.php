<?php

namespace App\Services;

use App\Models\ServiceProvider;
use Illuminate\Support\Str;

class TenantService
{
    public function resolveByApiKey(string $apiKey): ?ServiceProvider
    {
        return ServiceProvider::where('api_key', $apiKey)->first();
    }

    public function generateApiCredentials(): array
    {
        return [
            'api_key'    => Str::random(64),
            'api_secret' => Str::random(128),
        ];
    }

    public function isActive(ServiceProvider $provider): bool
    {
        return $provider->status === 'active';
    }
}
