<?php

namespace App\Livewire\Admin;

use App\Services\ReportingService;
use Livewire\Component;

class AdminUsers extends Component
{
    public string $search = '';
    public array $users = [];

    public function mount(ReportingService $reporting): void
    {
        $this->users = $reporting->getAllUsersForAdmin();
    }

    public function updatedSearch(ReportingService $reporting): void
    {
        $this->users = $reporting->getAllUsersForAdmin($this->search);
    }

    public function render()
    {
        return view('livewire.admin.admin-users')
            ->layout('layouts.admin', ['title' => 'Users']);
    }
}
