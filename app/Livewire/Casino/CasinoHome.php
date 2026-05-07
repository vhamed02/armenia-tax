<?php

namespace App\Livewire\Casino;

use App\Models\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CasinoHome extends Component
{
    public bool $kycVerified = false;
    public array $games = [];

    public function mount(): void
    {
        if (Auth::check()) {
            $provider = ServiceProvider::where('slug', 'softconstruct')->first();
            if ($provider) {
                $profile = Auth::user()->casinoProfiles()
                    ->where('service_provider_id', $provider->id)
                    ->first();
                $this->kycVerified = $profile && $profile->kyc_status === 'verified';
            }
        }

        $this->games = [
            ['name' => 'Book of Dead',      'category' => 'Slots',  'color' => '#1a2235', 'icon' => '📖', 'provider' => 'Play\'n GO'],
            ['name' => 'Sweet Bonanza',     'category' => 'Slots',  'color' => '#1a1a2e', 'icon' => '🍭', 'provider' => 'Pragmatic'],
            ['name' => 'Lightning Roulette','category' => 'Live',   'color' => '#1a2010', 'icon' => '⚡', 'provider' => 'Evolution'],
            ['name' => 'Blackjack VIP',     'category' => 'Table',  'color' => '#0d1b2a', 'icon' => '🃏', 'provider' => 'Evolution'],
            ['name' => 'Gates of Olympus',  'category' => 'Slots',  'color' => '#1e1a35', 'icon' => '⚡', 'provider' => 'Pragmatic'],
            ['name' => 'Crazy Time',        'category' => 'Live',   'color' => '#1a2010', 'icon' => '🎡', 'provider' => 'Evolution'],
            ['name' => 'Starburst',         'category' => 'Slots',  'color' => '#0d1a2e', 'icon' => '⭐', 'provider' => 'NetEnt'],
            ['name' => 'Baccarat Pro',      'category' => 'Table',  'color' => '#1a1a0d', 'icon' => '🎴', 'provider' => 'Evolution'],
            ['name' => 'Mega Moolah',       'category' => 'Slots',  'color' => '#1a2a0d', 'icon' => '🦁', 'provider' => 'Microgaming'],
            ['name' => 'Dream Catcher',     'category' => 'Live',   'color' => '#2a1a0d', 'icon' => '🎯', 'provider' => 'Evolution'],
            ['name' => 'Wolf Gold',         'category' => 'Slots',  'color' => '#1a1a1a', 'icon' => '🐺', 'provider' => 'Pragmatic'],
            ['name' => 'Speed Roulette',    'category' => 'Live',   'color' => '#0d1a1a', 'icon' => '🎰', 'provider' => 'Evolution'],
        ];
    }

    public function render()
    {
        return view('livewire.casino.casino-home')
            ->layout('layouts.casino');
    }
}
