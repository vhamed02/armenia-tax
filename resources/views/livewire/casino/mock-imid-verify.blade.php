<div style="display:flex;align-items:center;justify-content:center;min-height:100vh;padding:24px;">
    <div style="background:#ffffff;border-radius:16px;box-shadow:0 20px 60px rgba(0,0,0,0.4);width:100%;max-width:420px;overflow:hidden;">

        <div style="background:#1a3a2a;padding:28px;text-align:center;">
            <div style="display:inline-flex;align-items:center;gap:10px;margin-bottom:12px;">
                <div style="width:40px;height:40px;background:#00c853;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                    <svg width="22" height="22" fill="none" stroke="#000" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <span style="font-size:26px;font-weight:800;color:#ffffff;letter-spacing:-1px;">im<span style="color:#00c853;">ID</span></span>
            </div>
            <div style="font-size:18px;font-weight:600;color:#ffffff;margin-bottom:6px;">Հաստատեք ձեր ինքնությունը</div>
            <div style="font-size:13px;color:#a0c8a0;">imID — Mobile Identity Armenia</div>
        </div>

        <div style="padding:28px;">
            <div style="background:#f8fffe;border:1px solid #e0f5e9;border-radius:8px;padding:12px 16px;margin-bottom:24px;display:flex;align-items:center;gap:10px;">
                <svg width="14" height="14" fill="none" stroke="#00c853" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <span style="font-size:12px;color:#2d6a4f;">Session: <span style="font-family:monospace;font-weight:600;">{{ substr($session, 0, 8) }}...</span></span>
            </div>

            @if(!$showConfirm)
                <div style="text-align:center;padding:20px 0;">
                    <div style="display:flex;justify-content:center;margin-bottom:20px;">
                        <div style="width:48px;height:48px;border:4px solid #e0f5e9;border-top-color:#00c853;border-radius:50%;animation:spin 0.8s linear infinite;"></div>
                    </div>
                    <div style="font-size:15px;font-weight:600;color:#1a202c;margin-bottom:8px;">Connecting to imID...</div>
                    <div style="font-size:13px;color:#718096;">Please wait while we establish a secure connection</div>

                    <div style="margin-top:24px;display:flex;flex-direction:column;gap:8px;">
                        @foreach(['Establishing secure channel','Verifying session token','Loading identity data'] as $i => $step)
                            <div style="display:flex;align-items:center;gap:10px;padding:8px 12px;background:#f8fffe;border-radius:6px;">
                                <div style="width:18px;height:18px;border-radius:50%;background:#00c853;display:flex;align-items:center;justify-content:center;flex-shrink:0;animation:pulse {{ 0.5 + $i * 0.3 }}s ease-in-out infinite;">
                                    <svg width="10" height="10" fill="none" stroke="#fff" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                                </div>
                                <span style="font-size:12px;color:#4a5568;">{{ $step }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <style>
                    @keyframes spin { to { transform: rotate(360deg); } }
                    @keyframes pulse { 0%,100% { opacity:1; } 50% { opacity:0.5; } }
                </style>

                <script>
                    setTimeout(function() {
                        @this.showConfirmation();
                    }, 2000);
                </script>

            @else
                <div style="margin-bottom:20px;">
                    <div style="font-size:15px;font-weight:600;color:#1a202c;margin-bottom:16px;">Confirm Your Identity</div>

                    <div style="background:#f7fafc;border:1px solid #e2e8f0;border-radius:8px;padding:16px;margin-bottom:12px;">
                        <div style="font-size:11px;color:#718096;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px;">National ID</div>
                        <div style="font-size:18px;font-weight:700;color:#1a202c;font-family:monospace;">{{ $nationalId }}</div>
                    </div>

                    <div style="background:#f7fafc;border:1px solid #e2e8f0;border-radius:8px;padding:16px;margin-bottom:20px;">
                        <div style="font-size:11px;color:#718096;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px;">Full Name</div>
                        <div style="font-size:16px;font-weight:600;color:#1a202c;">{{ $userName }}</div>
                    </div>

                    <div style="background:#f0fff4;border:1px solid #c6f6d5;border-radius:8px;padding:12px;margin-bottom:20px;font-size:12px;color:#276749;display:flex;align-items:center;gap:8px;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        Your data is encrypted and securely transmitted
                    </div>

                    <form method="POST" action="{{ $callback }}?session={{ $session }}&status=success">
                        @csrf
                        <button type="submit" style="width:100%;background:#00c853;color:#000;border:none;border-radius:8px;padding:13px;font-size:15px;font-weight:700;cursor:pointer;margin-bottom:10px;">
                            ✓ Confirm Identity
                        </button>
                    </form>

                    <form method="POST" action="{{ $callback }}?session={{ $session }}&status=failed">
                        @csrf
                        <button type="submit" style="width:100%;background:transparent;color:#718096;border:none;padding:10px;font-size:13px;cursor:pointer;text-decoration:underline;">
                            Cancel
                        </button>
                    </form>
                </div>
            @endif
        </div>

        <div style="background:#f7fafc;border-top:1px solid #e2e8f0;padding:12px;text-align:center;">
            <div style="font-size:11px;color:#a0aec0;">Powered by imID · Ministry of High-Tech Industry of Armenia</div>
        </div>
    </div>
</div>
