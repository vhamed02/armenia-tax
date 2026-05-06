<?php

namespace App\Livewire\Admin;

use App\Models\TaxReport;
use App\Services\ReportingService;
use Livewire\Component;

class AdminReports extends Component
{
    public string $filter = '';
    public array $reports = [];
    public array $submitted = [];

    public function mount(ReportingService $reporting): void
    {
        $this->loadReports($reporting);
    }

    public function setFilter(string $filter, ReportingService $reporting): void
    {
        $this->filter    = $filter;
        $this->submitted = [];
        $this->loadReports($reporting);
    }

    public function submitReport(int $reportId, ReportingService $reporting): void
    {
        $report = TaxReport::find($reportId);

        if ($report && $report->status === 'pending') {
            $reporting->submitTaxReport($report);
            $this->submitted[$reportId] = true;
            $this->loadReports($reporting);
        }
    }

    private function loadReports(ReportingService $reporting): void
    {
        $this->reports = $reporting->getAllTaxReports($this->filter);
    }

    public function render()
    {
        return view('livewire.admin.admin-reports')
            ->layout('layouts.admin', ['title' => 'Tax Reports']);
    }
}
