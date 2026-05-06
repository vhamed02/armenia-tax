<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Services\ReportingService;
use Livewire\Component;

class AdminUserDetail extends Component
{
    public int $userId;
    public array $report = [];

    public function mount(int $id, ReportingService $reporting): void
    {
        $user          = User::with(['kycProfile', 'bankAccounts', 'transactions', 'taxReports', 'notifications', 'scanningJobs'])->findOrFail($id);
        $this->userId  = $id;
        $this->report  = $reporting->getUserFullReport($user);
    }

    public function render()
    {
        return view('livewire.admin.admin-user-detail')
            ->layout('layouts.admin', ['title' => 'User Report']);
    }
}
