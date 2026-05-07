<div>
    <div style="background:linear-gradient(135deg,#0d1b3e 0%,#0a2a1a 50%,#0a1a0d 100%);padding:80px 48px;position:relative;overflow:hidden;min-height:420px;display:flex;align-items:center;">
        <div style="position:absolute;top:0;left:0;right:0;bottom:0;background:radial-gradient(ellipse at 30% 50%,#00c85315 0%,transparent 60%),radial-gradient(ellipse at 70% 30%,#1565c015 0%,transparent 60%);"></div>
        <div style="position:relative;z-index:1;max-width:640px;">
            <div style="display:inline-flex;align-items:center;gap:8px;background:#00c85320;border:1px solid #00c85340;border-radius:20px;padding:6px 14px;font-size:12px;color:#00c853;font-weight:600;margin-bottom:20px;letter-spacing:0.5px;">
                ✓ LICENSED & REGULATED — ARFIMS INTEGRATED
            </div>
            <h1 style="font-size:48px;font-weight:800;color:#ffffff;line-height:1.1;margin:0 0 16px;">Your Premier<br><span style="color:#00c853;">Gaming Destination</span></h1>
            <p style="font-size:18px;color:#8892a4;margin:0 0 32px;line-height:1.6;">Secure. Verified. Licensed.<br>Powered by ARFIMS identity & financial monitoring.</p>
            <div style="display:flex;gap:12px;flex-wrap:wrap;">
                @auth
                    <a href="{{ route('casino.account') }}" style="background:#00c853;color:#000;padding:14px 28px;border-radius:8px;font-size:15px;font-weight:700;text-decoration:none;">
                        {{ $kycVerified ? 'Play Now' : 'Verify & Play' }}
                    </a>
                @else
                    <a href="{{ route('casino.register') }}" style="background:#00c853;color:#000;padding:14px 28px;border-radius:8px;font-size:15px;font-weight:700;text-decoration:none;">Get Started</a>
                    <a href="{{ route('casino.login') }}" style="background:transparent;color:#fff;padding:14px 28px;border-radius:8px;font-size:15px;font-weight:600;text-decoration:none;border:1px solid #1e2d45;">Sign In</a>
                @endauth
            </div>
        </div>
        <div style="position:absolute;right:48px;top:50%;transform:translateY(-50%);display:flex;gap:16px;opacity:0.6;">
            <div style="display:flex;flex-direction:column;gap:16px;">
                @foreach(['🎰','🃏','🎲'] as $icon)
                    <div style="width:64px;height:64px;background:#111827;border:1px solid #1e2d45;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:28px;">{{ $icon }}</div>
                @endforeach
            </div>
            <div style="display:flex;flex-direction:column;gap:16px;margin-top:32px;">
                @foreach(['⭐','🎯','🏆'] as $icon)
                    <div style="width:64px;height:64px;background:#111827;border:1px solid #1e2d45;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:28px;">{{ $icon }}</div>
                @endforeach
            </div>
        </div>
    </div>

    <div style="max-width:1200px;margin:0 auto;padding:48px 24px;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;">
            <div>
                <h2 style="font-size:24px;font-weight:700;color:#fff;margin:0 0 4px;">Popular Games</h2>
                <p style="font-size:14px;color:#8892a4;margin:0;">Top picks from our collection</p>
            </div>
            @if(!$kycVerified)
                <div style="background:#f4433615;border:1px solid #f4433630;border-radius:8px;padding:8px 16px;font-size:13px;color:#f44336;">
                    ⚠ KYC verification required to play
                </div>
            @endif
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:16px;margin-bottom:56px;">
            @foreach($games as $game)
                <div style="background:#111827;border:1px solid #1e2d45;border-radius:12px;overflow:hidden;transition:border-color 0.2s;" onmouseover="this.style.borderColor='#00c85350'" onmouseout="this.style.borderColor='#1e2d45'">
                    <div style="height:120px;background:{{ $game['color'] }};display:flex;align-items:center;justify-content:center;font-size:48px;position:relative;">
                        {{ $game['icon'] }}
                        <div style="position:absolute;top:8px;right:8px;background:#00000060;border-radius:4px;padding:2px 6px;font-size:10px;color:#8892a4;">{{ $game['provider'] }}</div>
                    </div>
                    <div style="padding:12px;">
                        <div style="font-size:14px;font-weight:600;color:#fff;margin-bottom:4px;">{{ $game['name'] }}</div>
                        <div style="margin-bottom:10px;">
                            @php $catColor = match($game['category']) { 'Live' => '#ffd600', 'Table' => '#1565c0', default => '#00c853' }; @endphp
                            <span style="background:{{ $catColor }}20;color:{{ $catColor }};font-size:10px;font-weight:600;padding:2px 8px;border-radius:4px;">{{ $game['category'] }}</span>
                        </div>
                        @if($kycVerified)
                            <button style="width:100%;background:#00c853;color:#000;border:none;border-radius:6px;padding:7px;font-size:12px;font-weight:700;cursor:pointer;">Play Now</button>
                        @else
                            <div style="position:relative;" title="KYC verification required">
                                <button disabled style="width:100%;background:#1a2235;color:#8892a4;border:1px solid #1e2d45;border-radius:6px;padding:7px;font-size:12px;font-weight:600;cursor:not-allowed;">🔒 KYC Required</button>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-bottom:56px;">
            @foreach([
                ['icon'=>'🛡','title'=>'Licensed & Regulated','desc'=>'Fully compliant with Armenian Revenue Service regulations via ARFIMS integration.','color'=>'#00c853'],
                ['icon'=>'⚡','title'=>'Instant Payouts','desc'=>'Verified users enjoy same-day withdrawals directly to their registered bank accounts.','color'=>'#ffd600'],
                ['icon'=>'🔒','title'=>'Secure Transactions','desc'=>'Every transaction is monitored and reported to ARFIMS for full financial transparency.','color'=>'#1565c0'],
            ] as $feature)
                <div style="background:#111827;border:1px solid #1e2d45;border-radius:12px;padding:28px;">
                    <div style="font-size:32px;margin-bottom:14px;">{{ $feature['icon'] }}</div>
                    <div style="font-size:16px;font-weight:700;color:#fff;margin-bottom:8px;">{{ $feature['title'] }}</div>
                    <div style="font-size:13px;color:#8892a4;line-height:1.6;">{{ $feature['desc'] }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <div style="background:#070b14;border-top:1px solid #1e2d45;padding:24px;text-align:center;">
        <div style="font-size:13px;color:#8892a4;">Demo Platform — ARFIMS Integration Showcase</div>
        <div style="font-size:11px;color:#4a5568;margin-top:4px;">Armenian Revenue & Financial Intelligence Monitoring System</div>
    </div>
</div>
