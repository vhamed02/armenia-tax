<?php

namespace App\Livewire\Admin;

use App\Services\MonitoringService;
use Livewire\Component;

class AdminTenants extends Component
{
    public array $tenants = [];

    public function mount(MonitoringService $monitoring): void
    {
        $this->tenants = $monitoring->getTenantOverview();
    }

    public function toggleStatus(int $providerId, MonitoringService $monitoring): void
    {
        $monitoring->toggleProviderStatus($providerId);
        $this->tenants = $monitoring->getTenantOverview();
    }

    public function render()
    {
        return view('livewire.admin.admin-tenants')
            ->layout('layouts.admin', ['title' => 'Service Providers']);
    }
}
