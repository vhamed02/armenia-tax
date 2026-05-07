<div>
    <div class="card">
        <div style="padding:18px 20px;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between;">
            <div style="font-size:15px;font-weight:600;color:#1a202c;">Registered Service Providers</div>
            <div style="font-size:13px;color:#718096;">{{ count($tenants) }} provider{{ count($tenants) !== 1 ? 's' : '' }}</div>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Provider</th>
                    <th>Status</th>
                    <th>Total Users</th>
                    <th>Verified</th>
                    <th>Total Deposits</th>
                    <th>Total Withdrawals</th>
                    <th>Over Limit</th>
                    <th>Tax Due</th>
                    <th>Last Activity</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($tenants as $tenant)
                    <tr>
                        <td>
                            <div style="font-weight:600;color:#1a202c;">{{ $tenant['provider_name'] }}</div>
                            <div style="font-size:11px;color:#a0aec0;font-family:monospace;">{{ $tenant['slug'] }}</div>
                        </td>
                        <td>
                            @if($tenant['status'] === 'active')
                                <span class="badge" style="background:#c6f6d5;color:#276749;">Active</span>
                            @elseif($tenant['status'] === 'suspended')
                                <span class="badge" style="background:#fed7d7;color:#822727;">Suspended</span>
                            @else
                                <span class="badge" style="background:#fefcbf;color:#744210;">Pending</span>
                            @endif
                        </td>
                        <td style="font-weight:600;">{{ $tenant['total_users'] }}</td>
                        <td>
                            <span style="color:#38a169;font-weight:600;">{{ $tenant['verified_users'] }}</span>
                            <span style="color:#a0aec0;font-size:12px;"> / {{ $tenant['total_users'] }}</span>
                        </td>
                        <td style="color:#38a169;font-weight:600;">{{ number_format($tenant['total_deposits_amd']) }} AMD</td>
                        <td style="color:#e53e3e;font-weight:600;">{{ number_format($tenant['total_withdrawals_amd']) }} AMD</td>
                        <td>
                            @if($tenant['users_over_limit'] > 0)
                                <span style="background:#fed7d7;color:#822727;font-size:12px;font-weight:600;padding:2px 8px;border-radius:4px;">{{ $tenant['users_over_limit'] }}</span>
                            @else
                                <span style="color:#a0aec0;">0</span>
                            @endif
                        </td>
                        <td style="{{ $tenant['total_tax_due_amd'] > 0 ? 'color:#f6ad55;font-weight:600;' : 'color:#a0aec0;' }}">
                            {{ $tenant['total_tax_due_amd'] > 0 ? number_format($tenant['total_tax_due_amd']) . ' AMD' : '—' }}
                        </td>
                        <td style="font-size:12px;color:#718096;">{{ $tenant['last_activity_at'] ?? 'No activity' }}</td>
                        <td>
                            <button wire:click="toggleStatus({{ $tenant['id'] }})"
                                style="padding:4px 12px;border-radius:5px;font-size:12px;font-weight:600;cursor:pointer;border:1px solid;
                                {{ $tenant['status'] === 'active' ? 'background:#fff5f5;color:#e53e3e;border-color:#fed7d7;' : 'background:#f0fff4;color:#38a169;border-color:#c6f6d5;' }}">
                                {{ $tenant['status'] === 'active' ? 'Suspend' : 'Activate' }}
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="10" style="text-align:center;color:#a0aec0;padding:32px;">No service providers found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
