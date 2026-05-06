<?php

namespace App\Livewire\Admin;

use App\Services\ReportingService;
use Livewire\Component;

class AdminDashboard extends Component
{
    public array $stats = [];
    public array $topUsers = [];
    public array $chartLabels = [];
    public array $chartData = [];

    public function mount(ReportingService $reporting): void
    {
        $this->stats    = $reporting->getDashboardStats();
        $this->topUsers = $reporting->getTopUsersByExcess(10);

        $this->chartLabels = array_column($this->stats['monthly_flagged_chart'], 'label');
        $this->chartData   = array_column($this->stats['monthly_flagged_chart'], 'count');
    }

    public function render()
    {
        return view('livewire.admin.admin-dashboard')
            ->layout('layouts.admin', ['title' => 'Dashboard']);
    }
}
