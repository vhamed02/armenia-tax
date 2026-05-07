<?php

namespace App\Livewire\Casino;

use App\Models\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CasinoAccount extends Component
{
    public array $profile = [];
    public array $walletTransactions = [];
    public string $kycStatus = 'not_started';
    public int $walletBalance = 0;

    public function mount(): void
    {
        $this->loadProfile();
    }

    private function loadProfile(): void
    {
        $user     = Auth::user();
        $provider = ServiceProvider::where('slug', 'softconstruct')->first();

        if (!$provider) {
            return;
        }

        $casinoProfile = $user->casinoProfiles()
            ->where('service_provider_id', $provider->id)
            ->first();

        if (!$casinoProfile) {
            return;
        }

        $this->kycStatus     = $casinoProfile->kyc_status;
        $this->walletBalance = $casinoProfile->wallet_balance;

        $this->profile = [
            'id'                   => $casinoProfile->id,
            'kyc_status'           => $casinoProfile->kyc_status,
            'kyc_verified_at'      => $casinoProfile->kyc_verified_at?->format('M d, Y H:i'),
            'national_id_verified' => $casinoProfile->national_id_verified,
            'casino_username'      => $casinoProfile->casino_username,
            'wallet_balance'       => $casinoProfile->wallet_balance,
        ];

        $this->walletTransactions = $casinoProfile->walletTransactions()
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(fn($t) => [
                'id'         => $t->id,
                'type'       => $t->type,
                'amount'     => $t->amount,
                'status'     => $t->status,
                'created_at' => $t->created_at->format('M d, Y'),
            ])->toArray();
    }

    public function render()
    {
        return view('livewire.casino.casino-account')
            ->layout('layouts.casino');
    }
}
