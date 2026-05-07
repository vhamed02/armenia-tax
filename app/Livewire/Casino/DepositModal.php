<?php

namespace App\Livewire\Casino;

use App\Models\CasinoProfile;
use App\Models\ServiceProvider;
use App\Services\WalletService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DepositModal extends Component
{
    public bool $open = false;
    public int|string $amount = '';
    public string $state = 'idle';
    public string $errorMessage = '';
    public int $newBalance = 0;

    protected $listeners = ['openDepositModal' => 'openModal'];

    public function openModal(): void
    {
        $this->reset(['amount', 'state', 'errorMessage', 'newBalance']);
        $this->open = true;
    }

    public function close(): void
    {
        $this->open = false;
        $this->reset(['amount', 'state', 'errorMessage']);
    }

    public function setQuickAmount(int $value): void
    {
        $this->amount = $value;
    }

    public function submit(WalletService $walletService): void
    {
        $this->errorMessage = '';

        $parsed = (int) $this->amount;

        if ($parsed < 1000) {
            $this->errorMessage = 'Minimum deposit is 1,000 AMD.';
            $this->state = 'error';
            return;
        }

        if ($parsed > 5000000) {
            $this->errorMessage = 'Maximum deposit is 5,000,000 AMD.';
            $this->state = 'error';
            return;
        }

        $user     = Auth::user();
        $provider = ServiceProvider::where('slug', 'softconstruct')->first();

        if (!$provider) {
            $this->errorMessage = 'Service provider not found.';
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

        $result = $walletService->deposit($profile, $parsed);

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
        return view('livewire.casino.deposit-modal');
    }
}
