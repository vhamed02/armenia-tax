<?php

namespace App\Http\Middleware;

use App\Http\Helpers\ApiResponse;
use App\Services\TenantService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantApiAuth
{
    public function __construct(private readonly TenantService $tenantService) {}

    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-KEY');

        if (!$apiKey) {
            return ApiResponse::error('Missing X-API-KEY header.', 401);
        }

        $provider = $this->tenantService->resolveByApiKey($apiKey);

        if (!$provider) {
            return ApiResponse::error('Invalid API key.', 401);
        }

        if (!$this->tenantService->isActive($provider)) {
            return ApiResponse::error('Service provider is suspended or inactive.', 401);
        }

        $request->merge(['tenant' => $provider]);
        $request->attributes->set('tenant', $provider);

        return $next($request);
    }
}
