<div>
    @if(empty($reports))
        <div class="card" style="padding:40px;text-align:center;color:#a0aec0;">
            No tax reports have been generated for your account.
        </div>
    @else
        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Period</th>
                        <th>Total Income</th>
                        <th>Income Limit</th>
                        <th>Excess</th>
                        <th>Tax Rate</th>
                        <th>Tax Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reports as $report)
                        <tr>
                            <td style="font-size:13px;color:#718096;">{{ $report['period_start'] }} → {{ $report['period_end'] }}</td>
                            <td>{{ number_format($report['total_income']) }} AMD</td>
                            <td>{{ number_format($report['income_limit']) }} AMD</td>
                            <td style="color:#e53e3e;font-weight:600;">{{ number_format($report['excess_income']) }} AMD</td>
                            <td>{{ $report['tax_rate'] }}%</td>
                            <td style="color:#f6ad55;font-weight:600;">{{ number_format($report['tax_amount']) }} AMD</td>
                            <td>
                                <span class="badge badge-{{ $report['status'] }}">{{ ucfirst($report['status']) }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
