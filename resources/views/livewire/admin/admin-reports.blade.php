<div>
    <div style="display:flex;gap:8px;margin-bottom:20px;">
        <button wire:click="setFilter('')" class="filter-btn {{ $filter === '' ? 'active' : '' }}">All</button>
        <button wire:click="setFilter('pending')" class="filter-btn {{ $filter === 'pending' ? 'active' : '' }}">Pending</button>
        <button wire:click="setFilter('submitted')" class="filter-btn {{ $filter === 'submitted' ? 'active' : '' }}">Submitted</button>
        <button wire:click="setFilter('acknowledged')" class="filter-btn {{ $filter === 'acknowledged' ? 'active' : '' }}">Acknowledged</button>
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>User</th>
                    <th>National ID</th>
                    <th>Period</th>
                    <th>Total Income</th>
                    <th>Excess</th>
                    <th>Tax Amount</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $report)
                    <tr>
                        <td style="font-weight:500;">{{ $report['user_name'] }}</td>
                        <td style="font-family:monospace;color:#718096;">{{ $report['national_id'] }}</td>
                        <td style="font-size:12px;color:#718096;">{{ $report['period_start'] }} → {{ $report['period_end'] }}</td>
                        <td>{{ number_format($report['total_income']) }} AMD</td>
                        <td style="color:#e53e3e;font-weight:600;">{{ number_format($report['excess_income']) }} AMD</td>
                        <td style="color:#f6ad55;font-weight:600;">{{ number_format($report['tax_amount']) }} AMD</td>
                        <td>
                            <span class="badge badge-{{ $report['status'] }}">{{ ucfirst($report['status']) }}</span>
                        </td>
                        <td>
                            @if($report['status'] === 'pending')
                                @if(isset($submitted[$report['id']]))
                                    <span style="color:#38a169;font-size:13px;font-weight:500;">✓ Submitted</span>
                                @else
                                    <button wire:click="submitReport({{ $report['id'] }})" class="btn btn-success btn-sm">Submit</button>
                                @endif
                            @else
                                <span style="color:#a0aec0;font-size:12px;">{{ $report['submitted_at'] ? \Carbon\Carbon::parse($report['submitted_at'])->format('M d, Y') : '—' }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" style="text-align:center;color:#a0aec0;padding:24px;">No reports found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
