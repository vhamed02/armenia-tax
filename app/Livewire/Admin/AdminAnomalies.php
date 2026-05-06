<?php

namespace App\Livewire\Admin;

use App\Services\ReportingService;
use Livewire\Component;

class AdminAnomalies extends Component
{
    public string $severityFilter = '';
    public array $anomalies = [];

    public function mount(ReportingService $reporting): void
    {
        $this->loadAnomalies($reporting);
    }

    public function setFilter(string $severity, ReportingService $reporting): void
    {
        $this->severityFilter = $severity;
        $this->loadAnomalies($reporting);
    }

    private function loadAnomalies(ReportingService $reporting): void
    {
        $all = $reporting->getAnomaliesAcrossAllUsers();

        $this->anomalies = $this->severityFilter === ''
            ? $all
            : array_values(array_filter($all, fn($a) => $a['severity'] === $this->severityFilter));
    }

    public function render()
    {
        return view('livewire.admin.admin-anomalies')
            ->layout('layouts.admin', ['title' => 'Anomalies']);
    }
}
