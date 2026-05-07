<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VBet Demo — ARFIMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    @livewireStyles
    <style>
        * { font-family: 'Inter', sans-serif; box-sizing: border-box; }
        body { margin: 0; background: #0a0e1a; color: #ffffff; min-height: 100vh; }
        .casino-nav { position: fixed; top: 0; left: 0; right: 0; height: 64px; background: #070b14; border-bottom: 1px solid #1e2d45; z-index: 100; display: flex; align-items: center; padding: 0 24px; gap: 32px; }
        .casino-content { padding-top: 64px; min-height: 100vh; }
        .nav-logo { font-size: 22px; font-weight: 800; color: #00c853; letter-spacing: -0.5px; display: flex; align-items: center; gap: 8px; text-decoration: none; }
        .nav-logo .demo-badge { font-size: 10px; font-weight: 700; background: #1565c0; color: #fff; padding: 2px 6px; border-radius: 4px; letter-spacing: 1px; }
        .nav-links { display: flex; gap: 4px; flex: 1; justify-content: center; }
        .nav-link { color: #8892a4; font-size: 14px; font-weight: 500; padding: 8px 14px; border-radius: 6px; cursor: pointer; transition: color 0.2s; text-decoration: none; }
        .nav-link:hover { color: #ffffff; background: #111827; }
        .nav-right { display: flex; align-items: center; gap: 10px; margin-left: auto; }
        .btn-casino { padding: 8px 18px; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; border: none; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s; }
        .btn-green { background: #00c853; color: #000; }
        .btn-green:hover { background: #00e676; }
        .btn-outline { background: transparent; color: #ffffff; border: 1px solid #1e2d45; }
        .btn-outline:hover { border-color: #00c853; color: #00c853; }
        .wallet-chip { background: #111827; border: 1px solid #1e2d45; border-radius: 20px; padding: 6px 14px; font-size: 13px; font-weight: 600; color: #ffffff; display: flex; align-items: center; gap: 6px; }
        .wallet-chip.locked { color: #8892a4; }
        .kyc-badge-verified { background: #00c85320; color: #00c853; border: 1px solid #00c85340; font-size: 11px; font-weight: 600; padding: 4px 10px; border-radius: 12px; }
        .kyc-badge-unverified { background: #f4433620; color: #f44336; border: 1px solid #f4433640; font-size: 11px; font-weight: 600; padding: 4px 10px; border-radius: 12px; cursor: pointer; text-decoration: none; }
        .avatar-btn { width: 34px; height: 34px; border-radius: 50%; background: #1565c0; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700; cursor: pointer; position: relative; }
        .dropdown { position: absolute; top: 44px; right: 0; background: #111827; border: 1px solid #1e2d45; border-radius: 8px; min-width: 180px; padding: 6px; z-index: 200; }
        .dropdown a, .dropdown button { display: block; width: 100%; text-align: left; padding: 8px 12px; font-size: 13px; color: #8892a4; border-radius: 5px; text-decoration: none; background: none; border: none; cursor: pointer; }
        .dropdown a:hover, .dropdown button:hover { background: #1a2235; color: #ffffff; }
        .card-casino { background: #111827; border: 1px solid #1e2d45; border-radius: 12px; }
        .card-elevated { background: #1a2235; border: 1px solid #1e2d45; border-radius: 12px; }
    </style>
</head>
<body>
    <nav class="casino-nav">
        <a href="{{ route('casino.home') }}" class="nav-logo">
            VBet <span class="demo-badge">DEMO</span>
        </a>

        <div class="nav-links">
            <span class="nav-link">Sports</span>
            <span class="nav-link">Casino</span>
            <span class="nav-link">Live Casino</span>
            <span class="nav-link">Promotions</span>
        </div>

        <div class="nav-right">
            @auth
                @php
                    $provider = \App\Models\ServiceProvider::where('slug','softconstruct')->first();
                    $casinoProfile = $provider ? auth()->user()->casinoProfiles()->where('service_provider_id', $provider->id)->first() : null;
                    $kycVerified = $casinoProfile && $casinoProfile->kyc_status === 'verified';
                    $balance = $casinoProfile ? $casinoProfile->wallet_balance : 0;
                @endphp

                <div class="wallet-chip {{ !$kycVerified ? 'locked' : '' }}">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M16 12h2"/></svg>
                    {{ $kycVerified ? number_format($balance) . ' AMD' : '— AMD' }}
                </div>

                @if($kycVerified)
                    <span class="kyc-badge-verified">✓ Verified</span>
                @else
                    <a href="{{ route('casino.account') }}" class="kyc-badge-unverified">⚠ Verify Identity</a>
                @endif

                <div style="position:relative;" x-data="{ open: false }">
                    <div class="avatar-btn" @click="open = !open">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="dropdown" x-show="open" @click.outside="open = false" style="display:none;" x-cloak>
                        <div style="padding: 8px 12px; font-size: 12px; color: #8892a4; border-bottom: 1px solid #1e2d45; margin-bottom: 4px;">
                            {{ auth()->user()->name }}
                        </div>
                        <a href="{{ route('casino.account') }}">My Account</a>
                        <form method="POST" action="{{ route('casino.logout') }}">
                            @csrf
                            <button type="submit">Logout</button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('casino.login') }}" class="btn-casino btn-outline">Login</a>
                <a href="{{ route('casino.register') }}" class="btn-casino btn-green">Register</a>
            @endauth
        </div>
    </nav>

    <div class="casino-content">
        {{ $slot }}
    </div>

    <script src="//unpkg.com/alpinejs" defer></script>
    @livewireScripts
</body>
</html>
