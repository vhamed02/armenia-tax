<div>
    <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:20px;align-items:flex-end;">
        <div>
            <div style="font-size:11px;color:#718096;margin-bottom:4px;text-transform:uppercase;letter-spacing:0.5px;">Provider</div>
            <select wire:model.live="providerFilter" style="background:#fff;border:1px solid #e2e8f0;border-radius:5px;padding:7px 10px;font-size:13px;color:#2d3748;outline:none;min-width:160px;">
                <option value="">All Providers</option>
                @foreach($providers as $p)
                    <option value="{{ $p['id'] }}">{{ $p['name'] }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <div style="font-size:11px;color:#718096;margin-bottom:4px;text-transform:uppercase;letter-spacing:0.5px;">Type</div>
            <select wire:model.live="typeFilter" style="background:#fff;border:1px solid #e2e8f0;border-radius:5px;padding:7px 10px;font-size:13px;color:#2d3748;outline:none;">
                <option value="">All Types</option>
                <option value="deposit">Deposit</option>
                <option value="withdrawal">Withdrawal</option>
            </select>
        </div>
        <div>
            <div style="font-size:11px;color:#718096;margin-bottom:4px;text-transform:uppercase;letter-spacing:0.5px;">From</div>
            <input type="date" wire:model.live="dateFrom" style="background:#fff;border:1px solid #e2e8f0;border-radius:5px;padding:7px 10px;font-size:13px;color:#2d3748;outline:none;">
        </div>
        <div>
            <div style="font-size:11px;color:#718096;margin-bottom:4px;text-transform:uppercase;letter-spacing:0.5px;">To</div>
            <input type="date" wire:model.live="dateTo" style="background:#fff;border:1px solid #e2e8f0;border-radius:5px;padding:7px 10px;font-size:13px;color:#2d3748;outline:none;">
        </div>
        <div style="flex:1;min-width:200px;">
            <div style="font-size:11px;color:#718096;margin-bottom:4px;text-transform:uppercase;letter-spacing:0.5px;">Search</div>
            <input type="search" wire:model.live.debounce.300ms="search" placeholder="Name or national ID..."
                style="width:100%;background:#fff;border:1px solid #e2e8f0;border-radius:5px;padding:7px 10px;font-size:13px;color:#2d3748;outline:none;">
        </div>
        <div style="font-size:13px;color:#718096;padding-bottom:7px;">
            {{ $transactions->total() }} record{{ $transactions->total() !== 1 ? 's' : '' }}
        </div>
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Date / Time</th>
                    <th>Provider</th>
                    <th>User</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>KYC</th>
                    <th>Annual Limit</th>
                    <th>Income YTD</th>
                    <th>Remaining</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $tx)
                    <tr wire:click="toggleRow({{ $tx['transaction_id'] }}, {{ $tx['user_id'] }})"
                        style="cursor:pointer;{{ $expandedRow === $tx['transaction_id'] ? 'background:#f0f7ff;' : '' }}"
                        onmouseover="this.style.background='#f7f9fc'" onmouseout="this.style.background='{{ $expandedRow === $tx['transaction_id'] ? '#f0f7ff' : '' }}'">
                        <td style="font-size:12px;color:#718096;white-space:nowrap;">{{ $tx['datetime'] }}</td>
                        <td>
                            <span style="background:#ebf8ff;color:#2b6cb0;font-size:11px;font-weight:600;padding:2px 8px;border-radius:4px;">
                                {{ $tx['service_provider_name'] }}
                            </span>
                        </td>
                        <td>
                            <div style="font-weight:500;font-size:13px;">{{ $tx['user_name'] }}</div>
                            <div style="font-size:11px;color:#a0aec0;font-family:monospace;">{{ $tx['national_id'] }}</div>
                        </td>
                        <td>
                            <span style="background:{{ $tx['transaction_type']==='deposit' ? '#c6f6d5' : '#fed7d7' }};color:{{ $tx['transaction_type']==='deposit' ? '#276749' : '#822727' }};font-size:11px;font-weight:600;padding:2px 8px;border-radius:4px;">
                                {{ ucfirst($tx['transaction_type']) }}
                            </span>
                        </td>
                        <td style="font-weight:600;">{{ number_format($tx['amount_amd']) }} AMD</td>
                        <td>
                            @if($tx['kyc_status'] === 'verified')
                                <span style="color:#38a169;font-size:12px;font-weight:600;">✓ Verified</span>
                            @else
                                <span style="color:#a0aec0;font-size:12px;">{{ ucfirst(str_replace('_',' ',$tx['kyc_status'])) }}</span>
                            @endif
                        </td>
                        <td style="font-size:13px;">{{ $tx['user_annual_limit'] > 0 ? number_format($tx['user_annual_limit']) : '—' }}</td>
                        <td style="font-size:13px;font-weight:500;">{{ $tx['user_current_annual_income'] > 0 ? number_format($tx['user_current_annual_income']) : '—' }}</td>
                        <td style="font-size:13px;">
                            @if($tx['user_annual_limit'] > 0)
                                <span style="color:{{ $tx['is_over_limit'] ? '#e53e3e' : '#38a169' }};font-weight:500;">
                                    {{ $tx['is_over_limit'] ? '-' . number_format($tx['excess_amount']) : number_format($tx['remaining_limit']) }}
                                </span>
                            @else
                                <span style="color:#a0aec0;">—</span>
                            @endif
                        </td>
                        <td>
                            @if($tx['user_annual_limit'] > 0)
                                @if($tx['is_over_limit'])
                                    <span style="background:#fed7d7;color:#822727;font-size:11px;font-weight:600;padding:3px 8px;border-radius:10px;white-space:nowrap;">
                                        OVER LIMIT
                                    </span>
                                @else
                                    <span style="background:#c6f6d5;color:#276749;font-size:11px;font-weight:600;padding:3px 8px;border-radius:10px;white-space:nowrap;">
                                        Within Limit
                                    </span>
                                @endif
                            @else
                                <span style="color:#a0aec0;font-size:12px;">No KYC</span>
                            @endif
                        </td>
                    </tr>

                    @if($expandedRow === $tx['transaction_id'] && !empty($expandedDetail))
                        <tr>
                            <td colspan="10" style="background:#f0f7ff;padding:0;">
                                <div style="padding:20px 24px;border-top:2px solid #1e88e5;">
                                    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:16px;">
                                        @foreach([
                                            ['Annual Limit', number_format($expandedDetail['annual_income_limit']) . ' AMD', '#1a202c'],
                                            ['Income YTD', number_format($expandedDetail['current_year_income']) . ' AMD', '#1a202c'],
                                            ['Excess Amount', number_format($expandedDetail['excess_amount']) . ' AMD', $expandedDetail['is_over_limit'] ? '#e53e3e' : '#38a169'],
                                            ['Estimated Tax', number_format($expandedDetail['estimated_tax']) . ' AMD', '#f6ad55'],
                                        ] as [$label, $value, $color])
                                            <div style="background:#fff;border-radius:6px;padding:12px 14px;border:1px solid #e2e8f0;">
                                                <div style="font-size:11px;color:#718096;margin-bottom:4px;">{{ $label }}</div>
                                                <div style="font-size:15px;font-weight:700;color:{{ $color }};">{{ $value }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
                                        <div style="font-size:12px;color:#718096;">
                                            Risk: <span style="font-weight:600;color:#1a202c;">{{ ucfirst($expandedDetail['risk_level']) }}</span>
                                        </div>
                                        <div style="font-size:12px;color:#718096;">
                                            Wallet Txs: <span style="font-weight:600;color:#1a202c;">{{ $expandedDetail['wallet_transactions_count'] }}</span>
                                        </div>
                                        @if(!empty($expandedDetail['active_on_providers']))
                                            <div style="font-size:12px;color:#718096;">
                                                Active on: <span style="font-weight:600;color:#1a202c;">{{ implode(', ', $expandedDetail['active_on_providers']) }}</span>
                                            </div>
                                        @endif
                                        <a href="{{ route('admin.users.detail', $tx['user_id']) }}"
                                            style="margin-left:auto;background:#1e88e5;color:#fff;padding:6px 14px;border-radius:5px;font-size:12px;font-weight:600;text-decoration:none;">
                                            Full User Report →
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr><td colspan="10" style="text-align:center;color:#a0aec0;padding:32px;">No transactions found.</td></tr>
                @endforelse
            </tbody>
        </table>

        @if($transactions->hasPages())
            <div style="padding:16px 20px;border-top:1px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between;">
                <div style="font-size:13px;color:#718096;">
                    Showing {{ $transactions->firstItem() }}–{{ $transactions->lastItem() }} of {{ $transactions->total() }}
                </div>
                <div style="display:flex;gap:4px;">
                    @if($transactions->onFirstPage())
                        <span style="padding:5px 10px;border-radius:4px;font-size:13px;color:#a0aec0;border:1px solid #e2e8f0;">←</span>
                    @else
                        <button wire:click="previousPage" style="padding:5px 10px;border-radius:4px;font-size:13px;color:#1e88e5;border:1px solid #e2e8f0;background:#fff;cursor:pointer;">←</button>
                    @endif

                    @foreach($transactions->getUrlRange(max(1,$transactions->currentPage()-2), min($transactions->lastPage(),$transactions->currentPage()+2)) as $page => $url)
                        <button wire:click="gotoPage({{ $page }})"
                            style="padding:5px 10px;border-radius:4px;font-size:13px;border:1px solid;cursor:pointer;
                            {{ $page === $transactions->currentPage() ? 'background:#1e88e5;color:#fff;border-color:#1e88e5;' : 'background:#fff;color:#4a5568;border-color:#e2e8f0;' }}">
                            {{ $page }}
                        </button>
                    @endforeach

                    @if($transactions->hasMorePages())
                        <button wire:click="nextPage" style="padding:5px 10px;border-radius:4px;font-size:13px;color:#1e88e5;border:1px solid #e2e8f0;background:#fff;cursor:pointer;">→</button>
                    @else
                        <span style="padding:5px 10px;border-radius:4px;font-size:13px;color:#a0aec0;border:1px solid #e2e8f0;">→</span>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
