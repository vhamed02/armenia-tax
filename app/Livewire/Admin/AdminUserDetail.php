<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\WalletTransaction;
use App\Services\MonitoringService;
use App\Services\ReportingService;
use Livewire\Component;

class AdminUserDetail extends Component
{
    public int $userId;
    public array $report = [];
    public array $incomeStatus = [];
    public array $platformActivity = [];
    public array $walletHistory = [];

    public function mount(int $id, ReportingService $reporting, MonitoringService $monitoring): void
    {
        $user = User::with([
            'kycProfile', 'bankAccounts', 'transactions',
            'taxReports', 'notifications', 'scanningJobs',
            'casinoProfiles.serviceProvider',
        ])->findOrFail($id);

        $this->userId       = $id;
        $this->report       = $reporting->getUserFullReport($user);
        $this->incomeStatus = $monitoring->getUserIncomeStatus($id);

        $this->platformActivity = $user->casinoProfiles->map(function ($profile) {
            $provider = $profile->serviceProvider;
            $txs      = $profile->walletTransactions()->where('status', 'completed')->get();
            $lastTx   = $profile->walletTransactions()->orderByDesc('created_at')->first();

            return [
                'provider_name'    => $provider?->name ?? '—',
                'provider_slug'    => $provider?->slug ?? '—',
                'provider_status'  => $provider?->status ?? '—',
                'kyc_status'       => $profile->kyc_status,
                'wallet_balance'   => $profile->wallet_balance,
                'total_deposited'  => (int) $txs->where('type', 'deposit')->sum('amount'),
                'total_withdrawn'  => (int) $txs->where('type', 'withdrawal')->sum('amount'),
                'last_activity_at' => $lastTx?->created_at?->diffForHumans() ?? 'Never',
            ];
        })->toArray();

        $this->walletHistory = WalletTransaction::where('user_id', $id)
            ->with('serviceProvider')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($t) => [
                'id'            => $t->id,
                'provider_name' => $t->serviceProvider?->name ?? '—',
                'type'          => $t->type,
                'amount'        => $t->amount,
                'status'        => $t->status,
                'balance_before'=> $t->balance_before,
                'balance_after' => $t->balance_after,
                'created_at'    => $t->created_at->format('M d, Y H:i'),
            ])->toArray();
    }

    public function render()
    {
        return view('livewire.admin.admin-user-detail')
            ->layout('layouts.admin', ['title' => 'User Report']);
    }
}
