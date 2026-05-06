<div>
    <div style="margin-bottom:20px;max-width:360px;">
        <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search by name or national ID…">
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>National ID</th>
                    <th>Risk Level</th>
                    <th>Income Limit</th>
                    <th>Current Income</th>
                    <th>Status</th>
                    <th>Tax Due</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td style="font-weight:500;">{{ $user['name'] }}</td>
                        <td style="font-family:monospace;color:#718096;">{{ $user['national_id'] }}</td>
                        <td>
                            <span class="badge badge-{{ $user['risk_level'] }}">{{ ucfirst($user['risk_level']) }}</span>
                        </td>
                        <td>{{ number_format($user['income_limit']) }} AMD</td>
                        <td>{{ number_format($user['total_income']) }} AMD</td>
                        <td>
                            @if($user['is_over_limit'])
                                <span class="badge" style="background:#fed7d7;color:#822727;">OVER LIMIT</span>
                            @else
                                <span class="badge" style="background:#c6f6d5;color:#276749;">OK</span>
                            @endif
                        </td>
                        <td style="{{ $user['tax_due'] > 0 ? 'color:#e53e3e;font-weight:600;' : 'color:#a0aec0;' }}">
                            {{ $user['tax_due'] > 0 ? number_format($user['tax_due']) . ' AMD' : '—' }}
                        </td>
                        <td>
                            <a href="{{ route('admin.users.detail', $user['id']) }}" class="btn btn-primary btn-sm">View</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" style="text-align:center;color:#a0aec0;padding:24px;">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
