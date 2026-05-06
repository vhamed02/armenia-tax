<?php

namespace App\Livewire\Portal;

use App\Services\IncomeAnalyzer;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UserDashboard extends Component
{
    public array $analysis = [];
    public array $recentTransactions = [];
    public int $unreadCount = 0;

    public function mount(IncomeAnalyzer $analyzer): void
    {
        $user = Auth::user()->load(['kycProfile', 'transactions', 'notifications']);

        $this->analysis = $analyzer->analyzeUser($user, 'annual');

        $this->recentTransactions = $user->transactions()
            ->orderByDesc('transaction_date')
            ->limit(20)
            ->get()
            ->map(fn($tx) => [
                'id'               => $tx->id,
                'transaction_date' => $tx->transaction_date->toDateString(),
                'description'      => $tx->description,
                'source_type'      => $tx->source_type,
                'amount'           => $tx->amount,
                'transaction_type' => $tx->transaction_type,
                'is_flagged'       => $tx->is_flagged,
            ])->toArray();

        $this->unreadCount = $user->notifications()->where('is_read', false)->count();
    }

    public function render()
    {
        return view('livewire.portal.user-dashboard')
            ->layout('layouts.portal', ['title' => 'My Dashboard']);
    }
}
