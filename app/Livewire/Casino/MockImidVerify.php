<?php

namespace App\Livewire\Casino;

use App\Models\CasinoProfile;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MockImidVerify extends Component
{
    public string $session = '';
    public string $callback = '';
    public bool $showConfirm = false;
    public string $nationalId = '';
    public string $userName = '';

    public function mount(): void
    {
        $this->session  = request()->query('session', '');
        $this->callback = request()->query('callback', '/casino/kyc/callback');

        if (Auth::check()) {
            $user             = Auth::user();
            $this->nationalId = $user->national_id ?? 'AB1234567';
            $this->userName   = $user->name;
        } else {
            $this->nationalId = 'AB1234567';
            $this->userName   = 'Guest User';
        }
    }

    public function showConfirmation(): void
    {
        $this->showConfirm = true;
    }

    public function render()
    {
        return view('livewire.casino.mock-imid-verify')
            ->layout('layouts.imid');
    }
}
