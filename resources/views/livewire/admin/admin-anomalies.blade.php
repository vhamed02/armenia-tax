<div>
    <div style="display:flex;gap:8px;margin-bottom:20px;">
        <button wire:click="setFilter('')" class="filter-btn {{ $severityFilter === '' ? 'active' : '' }}">All</button>
        <button wire:click="setFilter('high')" class="filter-btn {{ $severityFilter === 'high' ? 'active' : '' }}">High</button>
        <button wire:click="setFilter('medium')" class="filter-btn {{ $severityFilter === 'medium' ? 'active' : '' }}">Medium</button>
        <button wire:click="setFilter('low')" class="filter-btn {{ $severityFilter === 'low' ? 'active' : '' }}">Low</button>
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>User Name</th>
                    <th>National ID</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Source Type</th>
                    <th>Description</th>
                    <th>Severity</th>
                </tr>
            </thead>
            <tbody>
                @forelse($anomalies as $row)
                    <tr>
                        <td style="font-weight:500;">{{ $row['user_name'] }}</td>
                        <td style="font-family:monospace;color:#718096;">{{ $row['national_id'] }}</td>
                        <td style="color:#718096;">{{ $row['transaction_date'] }}</td>
                        <td style="font-weight:600;">{{ number_format($row['amount']) }} AMD</td>
                        <td>
                            <span style="background:#edf2f7;color:#4a5568;padding:2px 8px;border-radius:4px;font-size:12px;">{{ $row['source_type'] }}</span>
                        </td>
                        <td style="color:#718096;font-size:13px;">{{ $row['description'] }}</td>
                        <td>
                            @if($row['severity'] === 'high')
                                <span class="badge" style="background:#fed7d7;color:#822727;">High</span>
                            @elseif($row['severity'] === 'medium')
                                <span class="badge" style="background:#fefcbf;color:#744210;">Medium</span>
                            @else
                                <span class="badge" style="background:#e2e8f0;color:#4a5568;">Low</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" style="text-align:center;color:#a0aec0;padding:24px;">No anomalies found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
