<aside style="background:#0f1a2e;width:240px;min-height:100vh;position:fixed;top:0;left:0;display:flex;flex-direction:column;z-index:50;">
    <div style="padding:24px 20px 16px;">
        <div style="color:#ffffff;font-size:18px;font-weight:700;letter-spacing:0.5px;">ARFIMS</div>
        <div style="color:#a0aec0;font-size:11px;margin-top:2px;">Revenue Intelligence</div>
    </div>

    <nav style="flex:1;padding:8px 12px;">
        <a href="{{ route('admin.dashboard') }}"
           style="display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:6px;text-decoration:none;margin-bottom:2px;font-size:14px;
           {{ request()->routeIs('admin.dashboard') ? 'background:#1e88e5;color:#ffffff;' : 'color:#a0aec0;' }}">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
            Dashboard
        </a>
        <a href="{{ route('admin.users') }}"
           style="display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:6px;text-decoration:none;margin-bottom:2px;font-size:14px;
           {{ request()->routeIs('admin.users*') ? 'background:#1e88e5;color:#ffffff;' : 'color:#a0aec0;' }}">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            Users
        </a>
        <a href="{{ route('admin.reports') }}"
           style="display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:6px;text-decoration:none;margin-bottom:2px;font-size:14px;
           {{ request()->routeIs('admin.reports') ? 'background:#1e88e5;color:#ffffff;' : 'color:#a0aec0;' }}">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            Tax Reports
        </a>
        <a href="{{ route('admin.anomalies') }}"
           style="display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:6px;text-decoration:none;margin-bottom:2px;font-size:14px;
           {{ request()->routeIs('admin.anomalies') ? 'background:#1e88e5;color:#ffffff;' : 'color:#a0aec0;' }}">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            Anomalies
        </a>
        <a href="{{ route('admin.tenants') }}"
           style="display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:6px;text-decoration:none;margin-bottom:2px;font-size:14px;
           {{ request()->routeIs('admin.tenants') ? 'background:#1e88e5;color:#ffffff;' : 'color:#a0aec0;' }}">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
            Service Providers
        </a>
        <a href="{{ route('admin.transaction-log') }}"
           style="display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:6px;text-decoration:none;margin-bottom:2px;font-size:14px;
           {{ request()->routeIs('admin.transaction-log') ? 'background:#1e88e5;color:#ffffff;' : 'color:#a0aec0;' }}">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            Transaction Monitor
        </a>
    </nav>

    <div style="padding:16px 20px;border-top:1px solid #1e2d45;">
        <div style="color:#ffffff;font-size:13px;font-weight:500;">{{ auth()->user()->name }}</div>
        <div style="color:#a0aec0;font-size:11px;margin-bottom:10px;">Administrator</div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" style="background:none;border:1px solid #2d3f5a;color:#a0aec0;padding:6px 12px;border-radius:4px;font-size:12px;cursor:pointer;width:100%;">
                Sign Out
            </button>
        </form>
    </div>
</aside>
