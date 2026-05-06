<div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;">
        <div class="card" style="padding:24px;">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
                <div style="background:#ebf8ff;width:44px;height:44px;border-radius:50%;display:flex;align-items:center;justify-content:center;">
                    <svg width="20" height="20" fill="none" stroke="#1e88e5" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </div>
                <div>
                    <div style="font-size:18px;font-weight:700;color:#1a202c;">Hello, {{ auth()->user()->name }}</div>
                    <span style="background:#c6f6d5;color:#276749;font-size:11px;font-weight:600;padding:2px 8px;border-radius:10px;">✓ KYC Verified</span>
                </div>
            </div>
            <div style="font-size:13px;color:#718096;">National ID: <span style="font-family:monospace;color:#4a5568;">{{ auth()->user()->national_id }}</span></div>
        </div>

        <div class="card" style="padding:24px;">
            <div style="font-size:14px;font-weight:600;color:#718096;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:14px;">Income Summary — {{ now()->year }}</div>
            <div style="display:flex;flex-direction:column;gap:10px;">
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <span style="font-size:13px;color:#718096;">Annual Limit</span>
                    <span style="font-weight:600;">{{ number_format($analysis['income_limit']) }} AMD</span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <span style="font-size:13px;color:#718096;">Your Income This Year</span>
                    <span style="font-weight:600;">{{ number_format($analysis['total_income']) }} AMD</span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;padding-top:8px;border-top:1px solid #f0f4f8;">
                    <span style="font-size:13px;color:#718096;">Status</span>
                    @if($analysis['is_over_limit'])
                        <span style="background:#fed7d7;color:#822727;font-size:12px;font-weight:700;padding:3px 10px;border-radius:10px;">OVER LIMIT</span>
                    @else
                        <span style="background:#c6f6d5;color:#276749;font-size:12px;font-weight:700;padding:3px 10px;border-radius:10px;">Within Limit</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($analysis['is_over_limit'])
        <div class="alert-danger" style="margin-bottom:24px;">
            <div style="font-weight:600;font-size:15px;margin-bottom:6px;">⚠ Income Limit Exceeded</div>
            <div style="font-size:14px;line-height:1.6;">
                You have exceeded your declared income limit by <strong>{{ number_format($analysis['excess_income']) }} AMD</strong> ({{ $analysis['excess_percentage'] }}%).
                Estimated tax owed: <strong>{{ number_format($analysis['tax_breakdown']['tax_amount']) }} AMD</strong> at {{ $analysis['tax_breakdown']['total_rate'] }}%.
                A report has been filed with the State Revenue Committee.
            </div>
        </div>
    @endif

    <div class="card">
        <div style="padding:16px 20px;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between;">
            <div style="font-size:15px;font-weight:600;color:#1a202c;">Recent Transactions</div>
            @if($unreadCount > 0)
                <a href="{{ route('portal.notifications') }}" style="background:#e53e3e;color:#fff;font-size:12px;font-weight:600;padding:3px 10px;border-radius:10px;text-decoration:none;">{{ $unreadCount }} unread</a>
            @endif
        </div>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Source Type</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentTransactions as $tx)
                    <tr style="{{ $tx['is_flagged'] ? 'background:#fff5f5;' : '' }}">
                        <td style="color:#718096;">{{ $tx['transaction_date'] }}</td>
                        <td>{{ $tx['description'] }}</td>
                        <td><span style="background:#edf2f7;color:#4a5568;padding:2px 8px;border-radius:4px;font-size:12px;">{{ $tx['source_type'] }}</span></td>
                        <td style="font-weight:600;color:{{ $tx['transaction_type'] === 'credit' ? '#38a169' : '#e53e3e' }};">
                            {{ $tx['transaction_type'] === 'credit' ? '+' : '-' }}{{ number_format($tx['amount']) }} AMD
                        </td>
                        <td>
                            @if($tx['is_flagged'])
                                <span style="background:#fed7d7;color:#822727;font-size:11px;font-weight:600;padding:2px 8px;border-radius:10px;">🚩 Flagged</span>
                            @else
                                <span style="color:#a0aec0;font-size:12px;">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="text-align:center;color:#a0aec0;padding:24px;">No transactions found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
