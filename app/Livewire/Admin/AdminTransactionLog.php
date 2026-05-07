<?php

namespace App\Livewire\Admin;

use App\Models\ServiceProvider;
use App\Services\MonitoringService;
use Livewire\Component;
use Livewire\WithPagination;

class AdminTransactionLog extends Component
{
    use WithPagination;

    public string $providerFilter = '';
    public string $typeFilter = '';
    public string $dateFrom = '';
    public string $dateTo = '';
    public string $search = '';
    public ?int $expandedRow = null;
    public array $expandedDetail = [];
    public array $providers = [];

    public function mount(): void
    {
        $this->providers = ServiceProvider::all()
            ->map(fn($p) => ['id' => $p->id, 'name' => $p->name])
            ->toArray();
    }

    public function updatedProviderFilter(): void { $this->resetPage(); }
    public function updatedTypeFilter(): void { $this->resetPage(); }
    public function updatedDateFrom(): void { $this->resetPage(); }
    public function updatedDateTo(): void { $this->resetPage(); }
    public function updatedSearch(): void { $this->resetPage(); }

    public function toggleRow(int $txId, int $userId, MonitoringService $monitoring): void
    {
        if ($this->expandedRow === $txId) {
            $this->expandedRow    = null;
            $this->expandedDetail = [];
            return;
        }

        $this->expandedRow    = $txId;
        $this->expandedDetail = $monitoring->getUserIncomeStatus($userId);
    }

    public function render(MonitoringService $monitoring)
    {
        $filters = array_filter([
            'service_provider_id' => $this->providerFilter ?: null,
            'type'                => $this->typeFilter ?: null,
            'date_from'           => $this->dateFrom ?: null,
            'date_to'             => $this->dateTo ?: null,
            'search'              => $this->search ?: null,
        ]);

        $transactions = $monitoring->getTransactionLog($filters);

        return view('livewire.admin.admin-transaction-log', [
            'transactions' => $transactions,
        ])->layout('layouts.admin', ['title' => 'Transaction Monitor']);
    }
}
