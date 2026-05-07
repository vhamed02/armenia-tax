<div>
    <div style="margin-bottom:20px;">
        <a href="{{ route('admin.users') }}" style="color:#1e88e5;font-size:13px;text-decoration:none;">← Back to Users</a>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;">
        <div class="card" style="padding:20px 24px;">
            <div style="font-size:14px;font-weight:600;color:#718096;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:14px;">User Profile</div>
            <div style="display:flex;flex-direction:column;gap:8px;">
                <div style="display:flex;justify-content:space-between;"><span style="color:#718096;font-size:13px;">Name</span><span style="font-weight:500;">{{ $report['user']['name'] }}</span></div>
                <div style="display:flex;justify-content:space-between;"><span style="color:#718096;font-size:13px;">National ID</span><span style="font-family:monospace;">{{ $report['user']['national_id'] }}</span></div>
                <div style="display:flex;justify-content:space-between;"><span style="color:#718096;font-size:13px;">Email</span><span>{{ $report['user']['email'] }}</span></div>
                <div style="display:flex;justify-content:space-between;"><span style="color:#718096;font-size:13px;">Phone</span><span>{{ $report['user']['phone'] ?? '—' }}</span></div>
            </div>
        </div>

        <div class="card" style="padding:20px 24px;">
            <div style="font-size:14px;font-weight:600;color:#718096;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:14px;">KYC Profile</div>
            @if($report['kyc'])
                <div style="display:flex;flex-direction:column;gap:8px;">
                    <div style="display:flex;justify-content:space-between;"><span style="color:#718096;font-size:13px;">Status</span><span class="badge badge-{{ $report['kyc']['status'] }}">{{ ucfirst($report['kyc']['status']) }}</span></div>
                    <div style="display:flex;justify-content:space-between;"><span style="color:#718096;font-size:13px;">Risk Level</span><span class="badge badge-{{ $report['kyc']['risk_level'] }}">{{ ucfirst($report['kyc']['risk_level']) }}</span></div>
                    <div style="display:flex;justify-content:space-between;"><span style="color:#718096;font-size:13px;">Annual Limit</span><span style="font-weight:600;">{{ number_format($report['kyc']['annual_income_limit']) }} AMD</span></div>
                    <div style="display:flex;justify-content:space-between;"><span style="color:#718096;font-size:13px;">Employer</span><span>{{ $report['kyc']['employer_name'] }}</span></div>
                    <div style="display:flex;justify-content:space-between;"><span style="color:#718096;font-size:13px;">Occupation</span><span>{{ $report['kyc']['occupation'] }}</span></div>
                </div>
            @endif
        </div>
    </div>

    <div class="card" style="padding:20px 24px;margin-bottom:24px;">
        <div style="font-size:14px;font-weight:600;color:#718096;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:14px;">Income Analysis</div>
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;">
            <div>
                <div style="font-size:12px;color:#718096;">Total Income</div>
                <div style="font-size:18px;font-weight:700;color:#1a202c;">{{ number_format($report['income_analysis']['total_income']) }} <span style="font-size:12px;font-weight:400;color:#718096;">AMD</span></div>
            </div>
            <div>
                <div style="font-size:12px;color:#718096;">Income Limit</div>
                <div style="font-size:18px;font-weight:700;color:#1a202c;">{{ number_format($report['income_analysis']['income_limit']) }} <span style="font-size:12px;font-weight:400;color:#718096;">AMD</span></div>
            </div>
            <div>
                <div style="font-size:12px;color:#718096;">Excess Income</div>
                <div style="font-size:18px;font-weight:700;color:{{ $report['income_analysis']['excess_income'] > 0 ? '#e53e3e' : '#38a169' }};">{{ number_format($report['income_analysis']['excess_income']) }} <span style="font-size:12px;font-weight:400;color:#718096;">AMD</span></div>
            </div>
            <div>
                <div style="font-size:12px;color:#718096;">Tax Due ({{ $report['income_analysis']['tax_breakdown']['total_rate'] }}%)</div>
                <div style="font-size:18px;font-weight:700;color:#f6ad55;">{{ number_format($report['income_analysis']['tax_breakdown']['tax_amount']) }} <span style="font-size:12px;font-weight:400;color:#718096;">AMD</span></div>
            </div>
        </div>
    </div>

    <div class="card" style="margin-bottom:24px;">
        <div style="padding:16px 20px;border-bottom:1px solid #e2e8f0;font-size:14px;font-weight:600;color:#1a202c;">Recent Transactions</div>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Source</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Flagged</th>
                </tr>
            </thead>
            <tbody>
                @foreach(array_slice($report['transactions'], 0, 20) as $tx)
                    <tr style="{{ $tx['is_flagged'] ? 'background:#fff5f5;' : '' }}">
                        <td style="color:#718096;">{{ $tx['transaction_date'] }}</td>
                        <td>{{ $tx['description'] }}</td>
                        <td><span style="background:#edf2f7;color:#4a5568;padding:2px 8px;border-radius:4px;font-size:12px;">{{ $tx['source_type'] }}</span></td>
                        <td><span style="color:{{ $tx['type'] === 'credit' ? '#38a169' : '#e53e3e' }};font-weight:500;">{{ ucfirst($tx['type']) }}</span></td>
                        <td style="font-weight:600;">{{ number_format($tx['amount']) }} AMD</td>
                        <td>{{ $tx['is_flagged'] ? '🚩' : '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if(!empty($platformActivity))
    <div class="card" style="margin-bottom:24px;">
        <div style="padding:16px 20px;border-bottom:1px solid #e2e8f0;font-size:14px;font-weight:600;color:#1a202c;">Platform Activity</div>
        <table>
            <thead>
                <tr>
                    <th>Provider</th>
                    <th>KYC Status</th>
                    <th>Wallet Balance</th>
                    <th>Total Deposited</th>
                    <th>Total Withdrawn</th>
                    <th>Last Activity</th>
                </tr>
            </thead>
            <tbody>
                @foreach($platformActivity as $pa)
                    <tr>
                        <td>
                            <div style="font-weight:500;">{{ $pa['provider_name'] }}</div>
                            <div style="font-size:11px;color:#a0aec0;">{{ $pa['provider_slug'] }}</div>
                        </td>
                        <td>
                            @if($pa['kyc_status'] === 'verified')
                                <span class="badge" style="background:#c6f6d5;color:#276749;">✓ Verified</span>
                            @elseif($pa['kyc_status'] === 'in_progress')
                                <span class="badge" style="background:#fefcbf;color:#744210;">In Progress</span>
                            @elseif($pa['kyc_status'] === 'failed')
                                <span class="badge" style="background:#fed7d7;color:#822727;">Failed</span>
                            @else
                                <span class="badge" style="background:#e2e8f0;color:#4a5568;">Not Started</span>
                            @endif
                        </td>
                        <td style="font-weight:600;color:#38a169;">{{ number_format($pa['wallet_balance']) }} AMD</td>
                        <td style="color:#38a169;">{{ number_format($pa['total_deposited']) }} AMD</td>
                        <td style="color:#e53e3e;">{{ number_format($pa['total_withdrawn']) }} AMD</td>
                        <td style="font-size:12px;color:#718096;">{{ $pa['last_activity_at'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if(!empty($walletHistory))
    <div class="card" style="margin-bottom:24px;">
        <div style="padding:16px 20px;border-bottom:1px solid #e2e8f0;font-size:14px;font-weight:600;color:#1a202c;">Wallet Transaction History</div>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Provider</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Balance Before</th>
                    <th>Balance After</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($walletHistory as $wt)
                    <tr>
                        <td style="font-size:12px;color:#718096;">{{ $wt['created_at'] }}</td>
                        <td style="font-size:13px;">{{ $wt['provider_name'] }}</td>
                        <td>
                            <span style="background:{{ $wt['type']==='deposit' ? '#c6f6d5' : '#fed7d7' }};color:{{ $wt['type']==='deposit' ? '#276749' : '#822727' }};font-size:11px;font-weight:600;padding:2px 8px;border-radius:4px;">
                                {{ ucfirst($wt['type']) }}
                            </span>
                        </td>
                        <td style="font-weight:600;color:{{ $wt['type']==='deposit' ? '#38a169' : '#e53e3e' }};">
                            {{ $wt['type']==='deposit' ? '+' : '-' }}{{ number_format($wt['amount']) }} AMD
                        </td>
                        <td style="font-size:12px;color:#718096;">{{ number_format($wt['balance_before']) }}</td>
                        <td style="font-size:12px;font-weight:500;">{{ number_format($wt['balance_after']) }}</td>
                        <td>
                            <span style="background:{{ $wt['status']==='completed' ? '#c6f6d5' : '#e2e8f0' }};color:{{ $wt['status']==='completed' ? '#276749' : '#4a5568' }};font-size:11px;padding:2px 8px;border-radius:4px;">
                                {{ ucfirst($wt['status']) }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
