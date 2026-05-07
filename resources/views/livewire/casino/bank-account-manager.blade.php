<div style="background:#111827;border:1px solid #1e2d45;border-radius:12px;padding:28px;margin-top:24px;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
        <div style="display:flex;align-items:center;gap:10px;">
            <svg width="20" height="20" fill="none" stroke="#00c853" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
            <span style="font-size:16px;font-weight:700;color:#fff;">Bank Accounts</span>
        </div>
        <button wire:click="toggleForm"
            style="background:{{ $showForm ? '#1a2235' : '#00c853' }};color:{{ $showForm ? '#8892a4' : '#000' }};border:1px solid {{ $showForm ? '#1e2d45' : '#00c853' }};border-radius:6px;padding:7px 16px;font-size:13px;font-weight:600;cursor:pointer;">
            {{ $showForm ? '✕ Cancel' : '+ Add Account' }}
        </button>
    </div>

    @if(!empty($accounts))
        <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:{{ $showForm ? '20px' : '0' }};">
            @foreach($accounts as $account)
                <div style="background:#0a0e1a;border:1px solid {{ $account['is_primary'] ? '#00c85340' : '#1e2d45' }};border-radius:8px;padding:14px 16px;display:flex;align-items:center;justify-content:space-between;">
                    <div style="display:flex;align-items:center;gap:14px;">
                        <div style="width:36px;height:36px;background:#1a2235;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="18" height="18" fill="none" stroke="#8892a4" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                        </div>
                        <div>
                            <div style="font-size:14px;font-weight:600;color:#fff;">{{ $account['bank_name'] }}</div>
                            <div style="font-size:12px;color:#8892a4;font-family:monospace;">{{ $account['masked'] }} · {{ ucfirst($account['account_type']) }}</div>
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;">
                        @if($account['is_primary'])
                            <span style="background:#00c85320;color:#00c853;font-size:10px;font-weight:700;padding:3px 8px;border-radius:10px;letter-spacing:0.5px;">PRIMARY</span>
                        @else
                            <button wire:click="setPrimary({{ $account['id'] }})"
                                style="background:none;border:1px solid #1e2d45;color:#8892a4;border-radius:5px;padding:4px 10px;font-size:11px;cursor:pointer;">
                                Set Primary
                            </button>
                        @endif
                        <button wire:click="removeAccount({{ $account['id'] }})"
                            wire:confirm="Remove this bank account?"
                            style="background:none;border:none;color:#f4433660;cursor:pointer;padding:4px;font-size:16px;line-height:1;">
                            ×
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        @if(!$showForm)
            <div style="text-align:center;padding:24px;color:#8892a4;font-size:13px;">
                No bank accounts added yet. Add one to enable withdrawals.
            </div>
        @endif
    @endif

    @if($showForm)
        <div style="background:#0a0e1a;border:1px solid #1e2d45;border-radius:10px;padding:24px;">

            @if($verifyState === 'verified')
                <div style="text-align:center;padding:20px 0;">
                    <div style="width:56px;height:56px;background:#00c85320;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
                        <svg width="28" height="28" fill="none" stroke="#00c853" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <div style="font-size:16px;font-weight:700;color:#fff;margin-bottom:6px;">Account Verified!</div>
                    <div style="font-size:13px;color:#8892a4;margin-bottom:20px;">{{ $successMessage }}</div>
                    <button wire:click="toggleForm"
                        style="background:#00c853;color:#000;border:none;border-radius:8px;padding:10px 28px;font-size:14px;font-weight:700;cursor:pointer;">
                        Done
                    </button>
                </div>

            @elseif($verifyState === 'verifying')
                <div style="text-align:center;padding:20px 0;">
                    <div style="margin-bottom:20px;">
                        <div style="width:48px;height:48px;border:4px solid #00c85330;border-top-color:#00c853;border-radius:50%;animation:spin 0.8s linear infinite;margin:0 auto 16px;"></div>
                        <div style="font-size:15px;font-weight:600;color:#fff;margin-bottom:6px;">Verifying Account Details</div>
                        <div style="font-size:13px;color:#8892a4;">Checking cardholder name against account records...</div>
                    </div>

                    <div style="display:flex;flex-direction:column;gap:8px;max-width:320px;margin:0 auto 24px;">
                        @foreach([
                            ['Connecting to bank API', 400],
                            ['Validating account number', 900],
                            ['Matching cardholder identity', 1500],
                        ] as $i => [$step, $delay])
                            <div style="display:flex;align-items:center;gap:10px;padding:8px 12px;background:#111827;border-radius:6px;text-align:left;"
                                 x-data="{ done: false }"
                                 x-init="setTimeout(() => done = true, {{ $delay }})">
                                <div x-show="!done" style="width:16px;height:16px;border:2px solid #00c85330;border-top-color:#00c853;border-radius:50%;animation:spin 0.6s linear infinite;flex-shrink:0;"></div>
                                <div x-show="done" style="width:16px;height:16px;background:#00c853;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <svg width="10" height="10" fill="none" stroke="#000" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                                </div>
                                <span style="font-size:12px;color:#8892a4;">{{ $step }}</span>
                            </div>
                        @endforeach
                    </div>

                    <style>@keyframes spin { to { transform: rotate(360deg); } }</style>

                    <div x-data x-init="setTimeout(() => $wire.completeVerification(), 2200)"></div>
                </div>

            @elseif($verifyState === 'failed')
                <div style="background:#f4433615;border:1px solid #f4433630;border-radius:8px;padding:14px;margin-bottom:20px;color:#f44336;font-size:13px;display:flex;align-items:flex-start;gap:10px;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px;"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                    {{ $errorMessage }}
                </div>
                <button wire:click="resetForm"
                    style="width:100%;background:#1a2235;color:#8892a4;border:1px solid #1e2d45;border-radius:8px;padding:11px;font-size:14px;font-weight:600;cursor:pointer;">
                    Try Again
                </button>

            @else
                @if($errorMessage)
                    <div style="background:#f4433615;border:1px solid #f4433630;border-radius:8px;padding:12px 14px;margin-bottom:16px;color:#f44336;font-size:13px;">
                        {{ $errorMessage }}
                    </div>
                @endif

                <div style="background:#00c85310;border:1px solid #00c85330;border-radius:8px;padding:12px 14px;margin-bottom:20px;font-size:12px;color:#8892a4;display:flex;align-items:flex-start;gap:8px;">
                    <svg width="14" height="14" fill="none" stroke="#00c853" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px;"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    <span>The <strong style="color:#fff;">cardholder name on your bank account or card must exactly match your registered account name</strong>: <span style="color:#00c853;font-weight:600;">{{ auth()->user()->name }}</span>. This is required for identity verification.</span>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px;">
                    <div>
                        <label style="display:block;font-size:12px;color:#8892a4;margin-bottom:6px;">Bank Name <span style="color:#f44336;">*</span></label>
                        <select wire:model="bankName"
                            style="width:100%;background:#111827;border:1px solid #1e2d45;border-radius:8px;padding:10px 12px;color:#fff;font-size:14px;outline:none;cursor:pointer;"
                            onfocus="this.style.borderColor='#00c853'" onblur="this.style.borderColor='#1e2d45'">
                            <option value="" style="background:#111827;">Select bank...</option>
                            @foreach($banks as $bank)
                                <option value="{{ $bank }}" style="background:#111827;">{{ $bank }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;color:#8892a4;margin-bottom:6px;">Account Type <span style="color:#f44336;">*</span></label>
                        <select wire:model="accountType"
                            style="width:100%;background:#111827;border:1px solid #1e2d45;border-radius:8px;padding:10px 12px;color:#fff;font-size:14px;outline:none;cursor:pointer;"
                            onfocus="this.style.borderColor='#00c853'" onblur="this.style.borderColor='#1e2d45'">
                            <option value="checking" style="background:#111827;">Checking</option>
                            <option value="savings" style="background:#111827;">Savings</option>
                        </select>
                    </div>
                </div>

                <div style="margin-bottom:14px;">
                    <label style="display:block;font-size:12px;color:#8892a4;margin-bottom:6px;">Account / Card Number <span style="color:#f44336;">*</span></label>
                    <input type="text" wire:model="accountNumber" placeholder="e.g. AM1234567890"
                        style="width:100%;background:#111827;border:1px solid #1e2d45;border-radius:8px;padding:10px 14px;color:#fff;font-size:14px;outline:none;font-family:monospace;"
                        onfocus="this.style.borderColor='#00c853'" onblur="this.style.borderColor='#1e2d45'">
                </div>

                <div style="margin-bottom:20px;">
                    <label style="display:block;font-size:12px;color:#8892a4;margin-bottom:6px;">
                        Cardholder / Account Holder Name <span style="color:#f44336;">*</span>
                    </label>
                    <input type="text" wire:model="cardholderName"
                        style="width:100%;background:#111827;border:1px solid #1e2d45;border-radius:8px;padding:10px 14px;color:#fff;font-size:14px;outline:none;"
                        onfocus="this.style.borderColor='#00c853'" onblur="this.style.borderColor='#1e2d45'">
                    <div style="font-size:11px;color:#8892a4;margin-top:6px;display:flex;align-items:center;gap:5px;">
                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        Must match exactly: <strong style="color:#fff;">{{ auth()->user()->name }}</strong>
                    </div>
                </div>

                <button wire:click="startVerification" wire:loading.attr="disabled"
                    style="width:100%;background:#00c853;color:#000;border:none;border-radius:8px;padding:12px;font-size:14px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    <span wire:loading.remove wire:target="startVerification">Verify & Add Account</span>
                    <span wire:loading wire:target="startVerification">Verifying...</span>
                </button>
            @endif
        </div>
    @endif
</div>
