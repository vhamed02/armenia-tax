<div style="max-width:1100px;margin:0 auto;padding:40px 24px;">
    @if(session('success'))
        <div style="background:#00c85320;border:1px solid #00c85340;border-radius:8px;padding:14px 18px;margin-bottom:24px;color:#00c853;font-size:14px;font-weight:500;">
            ✓ {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="background:#f4433620;border:1px solid #f4433640;border-radius:8px;padding:14px 18px;margin-bottom:24px;color:#f44336;font-size:14px;font-weight:500;">
            ✗ {{ session('error') }}
        </div>
    @endif

    <div style="font-size:22px;font-weight:700;color:#fff;margin-bottom:28px;">My Account</div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;align-items:start;">

        <div style="background:#111827;border:1px solid #1e2d45;border-radius:12px;padding:28px;">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:24px;">
                <svg width="20" height="20" fill="none" stroke="#00c853" stroke-width="2" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                <span style="font-size:16px;font-weight:700;color:#fff;">Identity Verification</span>
            </div>

            <div style="background:#0a0e1a;border:1px solid #1e2d45;border-radius:10px;padding:20px;margin-bottom:20px;display:flex;align-items:center;gap:16px;">
                <div style="background:#1a3a2a;border-radius:8px;padding:10px 14px;display:flex;align-items:center;gap:8px;flex-shrink:0;">
                    <div style="width:28px;height:28px;background:#00c853;border-radius:4px;display:flex;align-items:center;justify-content:center;">
                        <svg width="16" height="16" fill="none" stroke="#000" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <span style="font-size:15px;font-weight:800;color:#fff;letter-spacing:-0.5px;">im<span style="color:#00c853;">ID</span></span>
                </div>
                <div>
                    <div style="font-size:12px;font-weight:600;color:#fff;">imID — Mobile Identity Armenia</div>
                    <div style="font-size:11px;color:#8892a4;margin-top:2px;">Official digital identity verification</div>
                </div>
            </div>

            @if($kycStatus === 'not_started')
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:16px;">
                    <span style="background:#8892a420;color:#8892a4;border:1px solid #8892a430;font-size:12px;font-weight:600;padding:4px 12px;border-radius:12px;">● Not Verified</span>
                </div>
                <p style="font-size:13px;color:#8892a4;margin:0 0 20px;line-height:1.6;">Verify your identity to unlock wallet features and start playing. The process takes less than 2 minutes.</p>
                <form method="POST" action="{{ route('casino.kyc.start') }}">
                    @csrf
                    <button type="submit" style="width:100%;background:#00c853;color:#000;border:none;border-radius:8px;padding:13px;font-size:14px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        Verify with imID
                    </button>
                </form>

            @elseif($kycStatus === 'in_progress')
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:16px;">
                    <span style="background:#ffd60020;color:#ffd600;border:1px solid #ffd60030;font-size:12px;font-weight:600;padding:4px 12px;border-radius:12px;">⏳ Verification in Progress</span>
                </div>
                <div style="display:flex;align-items:center;gap:12px;padding:16px;background:#0a0e1a;border-radius:8px;">
                    <div style="width:24px;height:24px;border:3px solid #ffd60030;border-top-color:#ffd600;border-radius:50%;animation:spin 0.8s linear infinite;flex-shrink:0;"></div>
                    <span style="font-size:13px;color:#8892a4;">Waiting for imID confirmation...</span>
                </div>
                <style>@keyframes spin { to { transform: rotate(360deg); } }</style>

            @elseif($kycStatus === 'verified')
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:16px;">
                    <span style="background:#00c85320;color:#00c853;border:1px solid #00c85340;font-size:12px;font-weight:600;padding:4px 12px;border-radius:12px;">✓ Identity Verified</span>
                </div>
                <div style="background:#00c85310;border:1px solid #00c85330;border-radius:8px;padding:16px;margin-bottom:12px;">
                    <div style="font-size:12px;color:#8892a4;margin-bottom:4px;">Verified National ID</div>
                    @php
                        $nid = $profile['national_id_verified'] ?? '';
                        $masked = strlen($nid) > 4 ? substr($nid,0,3) . str_repeat('*', strlen($nid)-4) . substr($nid,-1) : $nid;
                    @endphp
                    <div style="font-size:16px;font-weight:700;color:#fff;font-family:monospace;">{{ $masked }}</div>
                </div>
                <div style="font-size:12px;color:#8892a4;">Verified on {{ $profile['kyc_verified_at'] ?? '—' }}</div>

            @elseif($kycStatus === 'failed')
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:16px;">
                    <span style="background:#f4433620;color:#f44336;border:1px solid #f4433640;font-size:12px;font-weight:600;padding:4px 12px;border-radius:12px;">✗ Verification Failed</span>
                </div>
                <p style="font-size:13px;color:#8892a4;margin:0 0 20px;">Verification was unsuccessful. Please try again.</p>
                <form method="POST" action="{{ route('casino.kyc.start') }}">
                    @csrf
                    <button type="submit" style="width:100%;background:#f44336;color:#fff;border:none;border-radius:8px;padding:13px;font-size:14px;font-weight:700;cursor:pointer;">
                        Retry Verification
                    </button>
                </form>
            @endif
        </div>

        <div style="background:#111827;border:1px solid #1e2d45;border-radius:12px;padding:28px;position:relative;overflow:hidden;">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:24px;">
                <svg width="20" height="20" fill="none" stroke="#00c853" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M16 12h2"/></svg>
                <span style="font-size:16px;font-weight:700;color:#fff;">My Wallet</span>
            </div>

            @if($kycStatus !== 'verified')
                <div style="position:absolute;inset:0;background:#0a0e1acc;backdrop-filter:blur(2px);border-radius:12px;display:flex;flex-direction:column;align-items:center;justify-content:center;z-index:10;gap:12px;">
                    <div style="font-size:40px;">🔒</div>
                    <div style="font-size:14px;font-weight:600;color:#fff;text-align:center;max-width:220px;line-height:1.5;">Complete identity verification to access your wallet</div>
                </div>
            @endif

            <div style="background:#0a0e1a;border:1px solid #1e2d45;border-radius:10px;padding:24px;margin-bottom:20px;text-align:center;">
                <div style="font-size:12px;color:#8892a4;margin-bottom:8px;text-transform:uppercase;letter-spacing:1px;">Available Balance</div>
                <div style="font-size:36px;font-weight:800;color:#00c853;">{{ number_format($walletBalance) }}</div>
                <div style="font-size:14px;color:#8892a4;margin-top:4px;">AMD</div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:24px;">
                <button style="background:#00c853;color:#000;border:none;border-radius:8px;padding:12px;font-size:14px;font-weight:700;cursor:pointer;">
                    ↓ Deposit
                </button>
                <button style="background:transparent;color:#fff;border:1px solid #1e2d45;border-radius:8px;padding:12px;font-size:14px;font-weight:600;cursor:pointer;">
                    ↑ Withdraw
                </button>
            </div>

            @if(!empty($walletTransactions))
                <div style="font-size:13px;font-weight:600;color:#8892a4;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:12px;">Recent Transactions</div>
                <div style="display:flex;flex-direction:column;gap:8px;">
                    @foreach($walletTransactions as $tx)
                        <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 12px;background:#0a0e1a;border-radius:8px;">
                            <div style="display:flex;align-items:center;gap:10px;">
                                <span style="background:{{ $tx['type']==='deposit' ? '#00c85320' : '#f4433620' }};color:{{ $tx['type']==='deposit' ? '#00c853' : '#f44336' }};font-size:11px;font-weight:600;padding:2px 8px;border-radius:4px;">{{ ucfirst($tx['type']) }}</span>
                                <span style="font-size:12px;color:#8892a4;">{{ $tx['created_at'] }}</span>
                            </div>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <span style="font-size:14px;font-weight:600;color:{{ $tx['type']==='deposit' ? '#00c853' : '#f44336' }};">{{ $tx['type']==='deposit' ? '+' : '-' }}{{ number_format($tx['amount']) }}</span>
                                <span style="background:{{ $tx['status']==='completed' ? '#00c85320' : '#8892a420' }};color:{{ $tx['status']==='completed' ? '#00c853' : '#8892a4' }};font-size:10px;padding:2px 6px;border-radius:4px;">{{ $tx['status'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align:center;padding:24px;color:#8892a4;font-size:13px;">No transactions yet.</div>
            @endif
        </div>
    </div>
</div>
