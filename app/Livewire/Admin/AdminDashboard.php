<?php

namespace App\Livewire\Admin;

use App\Services\MonitoringService;
use App\Services\ReportingService;
use Livewire\Component;

class AdminDashboard extends Component
{
    public array $stats = [];
    public array $tenantStats = [];
    public array $topUsers = [];
    public array $chartLabels = [];
    public array $chartData = [];

    public function mount(ReportingService $reporting, MonitoringService $monitoring): void
    {
        $this->stats       = $reporting->getDashboardStats();
        $this->tenantStats = $monitoring->getDashboardTenantStats();
        $this->topUsers    = $reporting->getTopUsersByExcess(10);

        $this->chartLabels = array_column($this->stats['monthly_flagged_chart'], 'label');
        $this->chartData   = array_column($this->stats['monthly_flagged_chart'], 'count');
    }

    public function render()
    {
        return view('livewire.admin.admin-dashboard')
            ->layout('layouts.admin', ['title' => 'Dashboard']);
    }
}
