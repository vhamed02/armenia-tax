<div>
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:20px;">
        <div class="stat-card">
            <div class="stat-label">Users Monitored</div>
            <div class="stat-value" style="color:#1e88e5;">{{ $stats['total_users_monitored'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Over Income Limit</div>
            <div class="stat-value" style="color:#e53e3e;">{{ $stats['users_over_limit'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Tax Due</div>
            <div class="stat-value" style="color:#f6ad55;font-size:20px;">{{ number_format($stats['total_tax_due_amd']) }} <span style="font-size:13px;font-weight:400;color:#718096;">AMD</span></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Pending Reports</div>
            <div class="stat-value" style="color:#744210;">{{ $stats['reports_pending_submission'] }}</div>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:28px;">
        <div class="stat-card" style="border-top:3px solid #1e88e5;">
            <div class="stat-label">Active Service Providers</div>
            <div class="stat-value" style="color:#1e88e5;">{{ $tenantStats['active_service_providers'] }}</div>
        </div>
        <div class="stat-card" style="border-top:3px solid #38a169;">
            <div class="stat-label">Casino Wallet Txs Today</div>
            <div class="stat-value" style="color:#38a169;">{{ $tenantStats['casino_wallet_txs_today'] }}</div>
        </div>
        <div class="stat-card" style="border-top:3px solid #f6ad55;">
            <div class="stat-label">Total Wallet Volume</div>
            <div class="stat-value" style="color:#f6ad55;font-size:20px;">{{ number_format($tenantStats['total_wallet_volume_amd']) }} <span style="font-size:13px;font-weight:400;color:#718096;">AMD</span></div>
        </div>
        <div class="stat-card" style="border-top:3px solid #e53e3e;">
            <div class="stat-label">Cross-platform Over Limit</div>
            <div class="stat-value" style="color:#e53e3e;">{{ $tenantStats['cross_platform_users_over_limit'] }}</div>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:28px;">
        <div class="card" style="padding:20px 24px;">
            <div style="font-size:15px;font-weight:600;color:#1a202c;margin-bottom:16px;">Flagged Transactions — Last 6 Months</div>
            <canvas id="flaggedChart" height="180"></canvas>
        </div>
        <div class="card" style="padding:20px 24px;">
            <div style="font-size:15px;font-weight:600;color:#1a202c;margin-bottom:12px;">Quick Stats</div>
            <div style="display:flex;flex-direction:column;gap:12px;">
                <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid #f0f4f8;">
                    <span style="font-size:13px;color:#718096;">High Risk Users</span>
                    <span style="font-size:15px;font-weight:600;color:#e53e3e;">{{ $stats['high_risk_users_count'] }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid #f0f4f8;">
                    <span style="font-size:13px;color:#718096;">Total Flagged Transactions</span>
                    <span style="font-size:15px;font-weight:600;color:#1a202c;">{{ $stats['total_flagged_transactions'] }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;">
                    <span style="font-size:13px;color:#718096;">Scan Jobs Today</span>
                    <span style="font-size:15px;font-weight:600;color:#38a169;">{{ $stats['scan_jobs_today'] }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div style="padding:18px 20px;border-bottom:1px solid #e2e8f0;">
            <div style="font-size:15px;font-weight:600;color:#1a202c;">Top 10 Users by Excess Income</div>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>National ID</th>
                    <th>Risk Level</th>
                    <th>Income Limit</th>
                    <th>Actual Income</th>
                    <th>Excess Amount</th>
                    <th>Tax Due</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($topUsers as $user)
                    <tr>
                        <td style="font-weight:500;">{{ $user['name'] }}</td>
                        <td style="font-family:monospace;color:#718096;">{{ $user['national_id'] }}</td>
                        <td>
                            <span class="badge badge-{{ $user['risk_level'] }}">{{ ucfirst($user['risk_level']) }}</span>
                        </td>
                        <td>{{ number_format($user['income_limit']) }} AMD</td>
                        <td>{{ number_format($user['total_income']) }} AMD</td>
                        <td style="color:#e53e3e;font-weight:600;">{{ number_format($user['excess_income']) }} AMD</td>
                        <td style="color:#f6ad55;font-weight:600;">{{ number_format($user['tax_due']) }} AMD</td>
                        <td>
                            <a href="{{ route('admin.users.detail', $user['id']) }}" class="btn btn-primary btn-sm">View Report</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" style="text-align:center;color:#a0aec0;padding:24px;">No users over limit.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(!empty($tenantStats['top_providers_today']))
    <div class="card" style="margin-top:20px;">
        <div style="padding:18px 20px;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between;">
            <div style="font-size:15px;font-weight:600;color:#1a202c;">Provider Activity Today</div>
            <a href="{{ route('admin.tenants') }}" style="font-size:13px;color:#1e88e5;text-decoration:none;">View All →</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Provider</th>
                    <th>Status</th>
                    <th>Transactions Today</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tenantStats['top_providers_today'] as $p)
                    <tr>
                        <td style="font-weight:500;">{{ $p['name'] }}</td>
                        <td>
                            @if($p['status'] === 'active')
                                <span class="badge" style="background:#c6f6d5;color:#276749;">Active</span>
                            @else
                                <span class="badge" style="background:#fed7d7;color:#822727;">{{ ucfirst($p['status']) }}</span>
                            @endif
                        </td>
                        <td style="font-weight:600;color:#1e88e5;">{{ $p['today_count'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

<script>
    const ctx = document.getElementById('flaggedChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($chartLabels),
            datasets: [{
                label: 'Flagged Transactions',
                data: @json($chartData),
                backgroundColor: 'rgba(30,136,229,0.7)',
                borderColor: '#1e88e5',
                borderWidth: 1,
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: '#f0f4f8' } },
                x: { grid: { display: false } }
            }
        }
    });
});
</script>
