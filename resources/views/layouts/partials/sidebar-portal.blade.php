<aside style="background:#0f1a2e;width:240px;min-height:100vh;position:fixed;top:0;left:0;display:flex;flex-direction:column;z-index:50;">
    <div style="padding:24px 20px 16px;">
        <div style="color:#ffffff;font-size:18px;font-weight:700;letter-spacing:0.5px;">ARFIMS</div>
        <div style="color:#a0aec0;font-size:11px;margin-top:2px;">Taxpayer Portal</div>
    </div>

    <nav style="flex:1;padding:8px 12px;">
        <a href="{{ route('portal.dashboard') }}"
           style="display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:6px;text-decoration:none;margin-bottom:2px;font-size:14px;
           {{ request()->routeIs('portal.dashboard') ? 'background:#1e88e5;color:#ffffff;' : 'color:#a0aec0;' }}">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
            Dashboard
        </a>
        <a href="{{ route('portal.tax-reports') }}"
           style="display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:6px;text-decoration:none;margin-bottom:2px;font-size:14px;
           {{ request()->routeIs('portal.tax-reports') ? 'background:#1e88e5;color:#ffffff;' : 'color:#a0aec0;' }}">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            Tax Reports
        </a>
        <a href="{{ route('portal.notifications') }}"
           style="display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:6px;text-decoration:none;margin-bottom:2px;font-size:14px;position:relative;
           {{ request()->routeIs('portal.notifications') ? 'background:#1e88e5;color:#ffffff;' : 'color:#a0aec0;' }}">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            Notifications
            @php $unread = auth()->user()->notifications()->where('is_read', false)->count(); @endphp
            @if($unread > 0)
                <span style="background:#e53e3e;color:#fff;font-size:10px;font-weight:700;padding:1px 6px;border-radius:10px;margin-left:auto;">{{ $unread }}</span>
            @endif
        </a>
    </nav>

    <div style="padding:16px 20px;border-top:1px solid #1e2d45;">
        <div style="color:#ffffff;font-size:13px;font-weight:500;">{{ auth()->user()->name }}</div>
        <div style="color:#a0aec0;font-size:11px;margin-bottom:10px;">Taxpayer</div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" style="background:none;border:1px solid #2d3f5a;color:#a0aec0;padding:6px 12px;border-radius:4px;font-size:12px;cursor:pointer;width:100%;">
                Sign Out
            </button>
        </form>
    </div>
</aside>
