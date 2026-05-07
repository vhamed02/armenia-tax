<div>
    @if($open)
    <div style="position:fixed;inset:0;background:#00000090;z-index:1000;display:flex;align-items:center;justify-content:center;padding:16px;"
         wire:click.self="close">
        <div style="background:#111827;border:1px solid #1e2d45;border-radius:16px;width:100%;max-width:460px;overflow:hidden;box-shadow:0 24px 64px rgba(0,0,0,0.6);">

            <div style="display:flex;align-items:center;justify-content:space-between;padding:20px 24px;border-bottom:1px solid #1e2d45;">
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:32px;height:32px;background:#00c85320;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <svg width="16" height="16" fill="none" stroke="#00c853" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    </div>
                    <span style="font-size:16px;font-weight:700;color:#fff;">Add Funds to Wallet</span>
                </div>
                <button wire:click="close" style="background:none;border:none;color:#8892a4;cursor:pointer;font-size:20px;line-height:1;padding:4px;">×</button>
            </div>

            <div style="padding:24px;">
                @if($state === 'success')
                    <div style="text-align:center;padding:32px 16px;">
                        <div style="width:64px;height:64px;background:#00c85320;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                            <svg width="32" height="32" fill="none" stroke="#00c853" stroke-width="2.5" viewBox="0 0 24 24" style="animation:checkIn 0.4s ease-out;"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                        <div style="font-size:18px;font-weight:700;color:#fff;margin-bottom:8px;">Deposit Successful!</div>
                        <div style="font-size:14px;color:#8892a4;margin-bottom:20px;">Your new balance is</div>
                        <div style="font-size:32px;font-weight:800;color:#00c853;">{{ number_format($newBalance) }} <span style="font-size:16px;font-weight:400;color:#8892a4;">AMD</span></div>
                        <button wire:click="close" style="margin-top:24px;background:#00c853;color:#000;border:none;border-radius:8px;padding:12px 32px;font-size:14px;font-weight:700;cursor:pointer;">Done</button>
                    </div>
                    <style>@keyframes checkIn { from { stroke-dashoffset: 30; opacity:0; } to { stroke-dashoffset: 0; opacity:1; } }</style>

                @else
                    @if($state === 'error')
                        <div style="background:#f4433615;border:1px solid #f4433630;border-radius:8px;padding:12px 14px;margin-bottom:16px;color:#f44336;font-size:13px;">
                            ✗ {{ $errorMessage }}
                        </div>
                    @endif

                    <div style="background:#00c85310;border:1px solid #00c85330;border-radius:8px;padding:12px 14px;margin-bottom:20px;display:flex;align-items:center;gap:10px;">
                        <div style="background:#1a3a2a;border-radius:6px;padding:4px 8px;display:flex;align-items:center;gap:6px;flex-shrink:0;">
                            <div style="width:16px;height:16px;background:#00c853;border-radius:3px;display:flex;align-items:center;justify-content:center;">
                                <svg width="10" height="10" fill="none" stroke="#000" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                            </div>
                            <span style="font-size:12px;font-weight:700;color:#fff;">im<span style="color:#00c853;">ID</span></span>
                        </div>
                        <span style="font-size:12px;color:#00c853;font-weight:500;">Identity Verified — Wallet Active</span>
                    </div>

                    <div style="margin-bottom:20px;">
                        <label style="display:block;font-size:13px;color:#8892a4;margin-bottom:8px;">Amount (AMD)</label>
                        <div style="position:relative;">
                            <input type="number" wire:model="amount" min="1000" max="5000000" step="1000"
                                placeholder="Enter amount..."
                                style="width:100%;background:#0a0e1a;border:1px solid #1e2d45;border-radius:8px;padding:12px 60px 12px 14px;color:#fff;font-size:16px;font-weight:600;outline:none;"
                                onfocus="this.style.borderColor='#00c853'" onblur="this.style.borderColor='#1e2d45'">
                            <span style="position:absolute;right:14px;top:50%;transform:translateY(-50%);font-size:13px;color:#8892a4;font-weight:500;">AMD</span>
                        </div>
                    </div>

                    <div style="margin-bottom:20px;">
                        <div style="font-size:12px;color:#8892a4;margin-bottom:8px;">Quick amounts</div>
                        <div style="display:flex;gap:8px;flex-wrap:wrap;">
                            @foreach([10000, 50000, 100000, 500000] as $quick)
                                <button wire:click="setQuickAmount({{ $quick }})"
                                    style="background:{{ (int)$amount === $quick ? '#00c853' : '#1a2235' }};color:{{ (int)$amount === $quick ? '#000' : '#8892a4' }};border:1px solid {{ (int)$amount === $quick ? '#00c853' : '#1e2d45' }};border-radius:6px;padding:6px 14px;font-size:13px;font-weight:600;cursor:pointer;transition:all 0.15s;">
                                    {{ number_format($quick) }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div style="background:#1a2235;border-radius:8px;padding:12px 14px;margin-bottom:20px;font-size:12px;color:#8892a4;display:flex;align-items:center;gap:8px;">
                        <svg width="14" height="14" fill="none" stroke="#8892a4" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        Funds will be credited instantly to your casino wallet. Max single deposit: 5,000,000 AMD.
                    </div>

                    <button wire:click="submit" wire:loading.attr="disabled"
                        style="width:100%;background:#00c853;color:#000;border:none;border-radius:8px;padding:13px;font-size:15px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;">
                        <span wire:loading.remove wire:target="submit">Deposit Funds</span>
                        <span wire:loading wire:target="submit">Processing...</span>
                    </button>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>
