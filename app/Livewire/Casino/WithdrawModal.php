<?php

namespace App\Livewire\Casino;

use App\Models\BankAccount;
use App\Models\CasinoProfile;
use App\Models\ServiceProvider;
use App\Services\WalletService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class WithdrawModal extends Component
{
    public bool $open = false;
    public int|string $amount = '';
    public int|string $bankAccountId = '';
    public string $state = 'idle';
    public string $errorMessage = '';
    public int $newBalance = 0;
    public int $currentBalance = 0;
    public array $bankAccounts = [];

    protected $listeners = ['openWithdrawModal' => 'openModal', 'bankAccountAdded' => 'reloadAccounts'];

    public function openModal(): void
    {
        $this->reset(['amount', 'state', 'errorMessage', 'newBalance']);
        $this->loadData();
        $this->open = true;
    }

    public function reloadAccounts(): void
    {
        $this->loadData();
    }

    public function close(): void
    {
        $this->open = false;
        $this->reset(['amount', 'state', 'errorMessage']);
    }

    private function loadData(): void
    {
        $user     = Auth::user();
        $provider = ServiceProvider::where('slug', 'softconstruct')->first();

        if ($provider) {
            $profile = $user->casinoProfiles()
                ->where('service_provider_id', $provider->id)
                ->first();

            $this->currentBalance = $profile?->wallet_balance ?? 0;
        }

        $this->bankAccounts = $user->bankAccounts()
            ->get()
            ->map(fn($a) => [
                'id'             => $a->id,
                'bank_name'      => $a->bank_name,
                'account_number' => $a->account_number,
                'masked'         => $a->bank_name . ' ****' . substr($a->account_number, -4),
                'is_primary'     => $a->is_primary,
            ])->toArray();

        $primary = collect($this->bankAccounts)->firstWhere('is_primary', true);
        if ($primary) {
            $this->bankAccountId = $primary['id'];
        } elseif (!empty($this->bankAccounts)) {
            $this->bankAccountId = $this->bankAccounts[0]['id'];
        }
    }

    public function submit(WalletService $walletService): void
    {
        $this->errorMessage = '';

        $parsed = (int) $this->amount;

        if ($parsed <= 0) {
            $this->errorMessage = 'Please enter a valid amount.';
            $this->state = 'error';
            return;
        }

        if (!$this->bankAccountId) {
            $this->errorMessage = 'Please select a bank account.';
            $this->state = 'error';
            return;
        }

        $user        = Auth::user();
        $provider    = ServiceProvider::where('slug', 'softconstruct')->first();
        $bankAccount = BankAccount::where('id', $this->bankAccountId)
            ->where('user_id', $user->id)
            ->first();

        if (!$bankAccount) {
            $this->errorMessage = 'Invalid bank account.';
            $this->state = 'error';
            return;
        }

        $profile = $user->casinoProfiles()
            ->where('service_provider_id', $provider->id)
            ->first();

        if (!$profile) {
            $this->errorMessage = 'Casino profile not found.';
            $this->state = 'error';
            return;
        }

        $result = $walletService->withdraw($profile, $parsed, $bankAccount);

        if (!$result['success']) {
            $this->errorMessage = $result['error'];
            $this->state = 'error';
            return;
        }

        $this->newBalance = $result['new_balance'];
        $this->state = 'success';
        $this->dispatch('walletUpdated', balance: $result['new_balance']);
    }

    public function render()
    {
        return view('livewire.casino.withdraw-modal');
    }
}
