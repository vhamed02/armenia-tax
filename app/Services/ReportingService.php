<?php

namespace App\Services;

use App\Models\ScanningJob;
use App\Models\TaxReport;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Carbon;

class ReportingService
{
    public function __construct(private readonly IncomeAnalyzer $analyzer) {}

    public function getDashboardStats(): array
    {
        $totalUsers = User::where('is_admin', false)
            ->whereHas('kycProfile', fn($q) => $q->where('status', 'verified'))
            ->count();

        $yearStart = Carbon::now()->startOfYear()->toDateString();
        $yearEnd   = Carbon::now()->endOfYear()->toDateString();

        $usersOverLimit = User::where('is_admin', false)
            ->whereHas('kycProfile', fn($q) => $q->where('status', 'verified'))
            ->whereHas('taxReports', fn($q) => $q->whereBetween('report_period_start', [$yearStart, $yearEnd]))
            ->count();

        $totalFlagged = Transaction::where('is_flagged', true)->count();

        $totalTaxDue = (int) TaxReport::whereIn('status', ['pending', 'submitted'])
            ->sum('tax_amount');

        $reportsPending = TaxReport::where('status', 'pending')->count();

        $highRiskUsers = User::whereHas('kycProfile', fn($q) => $q->where('risk_level', 'high'))->count();

        $todayStart    = Carbon::now()->startOfDay();
        $scanJobsToday = ScanningJob::where('triggered_at', '>=', $todayStart)->count();

        $monthlyFlaggedChart = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthlyFlaggedChart[] = [
                'month' => $month->format('Y-m'),
                'label' => $month->format('M Y'),
                'count' => Transaction::where('is_flagged', true)
                    ->whereYear('transaction_date', $month->year)
                    ->whereMonth('transaction_date', $month->month)
                    ->count(),
            ];
        }

        return [
            'total_users_monitored'    => $totalUsers,
            'users_over_limit'         => $usersOverLimit,
            'total_flagged_transactions' => $totalFlagged,
            'total_tax_due_amd'        => $totalTaxDue,
            'reports_pending_submission' => $reportsPending,
            'high_risk_users_count'    => $highRiskUsers,
            'scan_jobs_today'          => $scanJobsToday,
            'monthly_flagged_chart'    => $monthlyFlaggedChart,
        ];
    }

    public function getUserFullReport(User $user): array
    {
        $user->load(['kycProfile', 'bankAccounts', 'transactions', 'taxReports', 'notifications', 'scanningJobs']);

        $kyc      = $user->kycProfile;
        $analysis = $this->analyzer->analyzeUser($user, 'annual');
        $anomalies = $this->analyzer->detectAnomalies($user);

        $transactions = $user->transactions()
            ->orderByDesc('transaction_date')
            ->get()
            ->map(fn($tx) => [
                'id'               => $tx->id,
                'type'             => $tx->transaction_type,
                'amount'           => $tx->amount,
                'description'      => $tx->description,
                'transaction_date' => $tx->transaction_date->toDateString(),
                'source_type'      => $tx->source_type,
                'is_flagged'       => $tx->is_flagged,
                'external_reference' => $tx->external_reference,
            ])->toArray();

        $taxReports = $user->taxReports()
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($r) => [
                'id'                   => $r->id,
                'period_start'         => $r->report_period_start->toDateString(),
                'period_end'           => $r->report_period_end->toDateString(),
                'total_income'         => $r->total_income,
                'income_limit'         => $r->income_limit,
                'excess_income'        => $r->excess_income,
                'tax_rate'             => $r->tax_rate,
                'tax_amount'           => $r->tax_amount,
                'status'               => $r->status,
                'submitted_to_gov_at'  => $r->submitted_to_gov_at?->toDateTimeString(),
                'metadata'             => $r->metadata,
            ]);

        $lastScan = $user->scanningJobs()->orderByDesc('triggered_at')->first();

        return [
            'user' => [
                'id'          => $user->id,
                'name'        => $user->name,
                'national_id' => $user->national_id,
                'email'       => $user->email,
                'phone'       => $user->phone,
            ],
            'kyc' => $kyc ? [
                'status'              => $kyc->status,
                'risk_level'          => $kyc->risk_level,
                'annual_income_limit' => $kyc->annual_income_limit,
                'occupation'          => $kyc->occupation,
                'employer_name'       => $kyc->employer_name,
                'kyc_verified_at'     => $kyc->kyc_verified_at?->toDateTimeString(),
            ] : null,
            'bank_accounts' => $user->bankAccounts->map(fn($a) => [
                'id'             => $a->id,
                'account_number' => $a->account_number,
                'bank_name'      => $a->bank_name,
                'account_type'   => $a->account_type,
                'balance'        => $a->balance,
                'is_primary'     => $a->is_primary,
            ])->toArray(),
            'income_analysis' => $analysis,
            'anomalies'       => $anomalies,
            'tax_reports'     => $taxReports,
            'transactions'    => $transactions,
            'last_scan'       => $lastScan ? [
                'triggered_at'         => $lastScan->triggered_at->toDateTimeString(),
                'completed_at'         => $lastScan->completed_at?->toDateTimeString(),
                'status'               => $lastScan->status,
                'transactions_scanned' => $lastScan->transactions_scanned,
                'anomalies_found'      => $lastScan->anomalies_found,
            ] : null,
        ];
    }

    public function submitTaxReport(TaxReport $report): TaxReport
    {
        $report->update([
            'status'              => 'submitted',
            'submitted_to_gov_at' => Carbon::now(),
        ]);

        return $report->fresh();
    }

    public function getTopUsersByExcess(int $limit = 10): array
    {
        $users = User::where('is_admin', false)
            ->whereHas('kycProfile', fn($q) => $q->where('status', 'verified'))
            ->with('kycProfile')
            ->get();

        $rows = [];
        foreach ($users as $user) {
            $analysis = $this->analyzer->analyzeUser($user, 'annual');
            if ($analysis['excess_income'] > 0) {
                $rows[] = [
                    'id'             => $user->id,
                    'name'           => $user->name,
                    'national_id'    => $user->national_id,
                    'risk_level'     => $user->kycProfile->risk_level,
                    'income_limit'   => $analysis['income_limit'],
                    'total_income'   => $analysis['total_income'],
                    'excess_income'  => $analysis['excess_income'],
                    'tax_due'        => $analysis['tax_breakdown']['tax_amount'],
                ];
            }
        }

        usort($rows, fn($a, $b) => $b['excess_income'] <=> $a['excess_income']);

        return array_slice($rows, 0, $limit);
    }

    public function getAllUsersForAdmin(string $search = ''): array
    {
        $query = User::where('is_admin', false)
            ->whereHas('kycProfile', fn($q) => $q->where('status', 'verified'))
            ->with('kycProfile');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('national_id', 'like', "%{$search}%");
            });
        }

        return $query->get()->map(function ($user) {
            $analysis = $this->analyzer->analyzeUser($user, 'annual');
            return [
                'id'           => $user->id,
                'name'         => $user->name,
                'national_id'  => $user->national_id,
                'risk_level'   => $user->kycProfile->risk_level,
                'income_limit' => $analysis['income_limit'],
                'total_income' => $analysis['total_income'],
                'is_over_limit'=> $analysis['is_over_limit'],
                'tax_due'      => $analysis['tax_breakdown']['tax_amount'],
            ];
        })->toArray();
    }

    public function getAllTaxReports(string $statusFilter = ''): array
    {
        $query = TaxReport::with('user')->orderByDesc('created_at');

        if ($statusFilter !== '') {
            $query->where('status', $statusFilter);
        }

        return $query->get()->map(fn($r) => [
            'id'           => $r->id,
            'user_id'      => $r->user_id,
            'user_name'    => $r->user->name,
            'national_id'  => $r->user->national_id,
            'period_start' => $r->report_period_start->toDateString(),
            'period_end'   => $r->report_period_end->toDateString(),
            'total_income' => $r->total_income,
            'income_limit' => $r->income_limit,
            'excess_income'=> $r->excess_income,
            'tax_rate'     => $r->tax_rate,
            'tax_amount'   => $r->tax_amount,
            'status'       => $r->status,
            'submitted_at' => $r->submitted_to_gov_at?->toDateTimeString(),
        ])->toArray();
    }

    public function getUnifiedTimeline(User $user): array
    {
        $bankTxs = $user->transactions()
            ->with('serviceProvider')
            ->orderByDesc('transaction_date')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($tx) => [
                'id'          => 'tx_' . $tx->id,
                'datetime'    => $tx->transaction_date->toDateString(),
                'sort_key'    => $tx->transaction_date->toDateString() . ' ' . $tx->created_at->format('H:i:s'),
                'channel'     => 'bank',
                'channel_label' => $tx->serviceProvider ? $tx->serviceProvider->name : 'Bank / Income',
                'direction'   => $tx->transaction_type === 'credit' ? 'in' : 'out',
                'amount'      => $tx->amount,
                'description' => $tx->description ?? '—',
                'source_type' => $tx->source_type,
                'is_flagged'  => $tx->is_flagged,
                'status'      => 'completed',
                'meta'        => null,
            ]);

        $walletTxs = \App\Models\WalletTransaction::where('user_id', $user->id)
            ->with('serviceProvider')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($tx) => [
                'id'          => 'wt_' . $tx->id,
                'datetime'    => $tx->created_at->toDateString(),
                'sort_key'    => $tx->created_at->format('Y-m-d H:i:s'),
                'channel'     => 'casino',
                'channel_label' => $tx->serviceProvider?->name ?? 'Casino',
                'direction'   => $tx->type === 'deposit' ? 'in' : 'out',
                'amount'      => $tx->amount,
                'description' => ucfirst($tx->type) . ' — ' . ($tx->serviceProvider?->name ?? 'Casino'),
                'source_type' => $tx->type,
                'is_flagged'  => false,
                'status'      => $tx->status,
                'meta'        => [
                    'balance_before' => $tx->balance_before,
                    'balance_after'  => $tx->balance_after,
                ],
            ]);

        $merged = $bankTxs->concat($walletTxs)
            ->sortByDesc('sort_key')
            ->values()
            ->toArray();

        return $merged;
    }

    public function getPlatformSummary(User $user): array
    {
        return $user->casinoProfiles()->with('serviceProvider')->get()->map(function ($profile) {
            $txs    = $profile->walletTransactions()->where('status', 'completed')->get();
            $lastTx = $profile->walletTransactions()->orderByDesc('created_at')->first();

            return [
                'provider_name'   => $profile->serviceProvider?->name ?? '—',
                'provider_slug'   => $profile->serviceProvider?->slug ?? '—',
                'kyc_status'      => $profile->kyc_status,
                'wallet_balance'  => $profile->wallet_balance,
                'total_deposited' => (int) $txs->where('type', 'deposit')->sum('amount'),
                'total_withdrawn' => (int) $txs->where('type', 'withdrawal')->sum('amount'),
                'tx_count'        => $txs->count(),
                'last_activity'   => $lastTx?->created_at?->diffForHumans() ?? 'Never',
            ];
        })->toArray();
    }

    public function getAnomaliesAcrossAllUsers(): array
    {
        $flagged = Transaction::where('is_flagged', true)
            ->with(['user.kycProfile', 'bankAccount'])
            ->orderByDesc('transaction_date')
            ->get();

        return $flagged->map(fn($tx) => [
            'transaction_id'   => $tx->id,
            'user_id'          => $tx->user_id,
            'user_name'        => $tx->user->name,
            'national_id'      => $tx->user->national_id,
            'risk_level'       => $tx->user->kycProfile?->risk_level,
            'amount'           => $tx->amount,
            'transaction_type' => $tx->transaction_type,
            'source_type'      => $tx->source_type,
            'description'      => $tx->description,
            'transaction_date' => $tx->transaction_date->toDateString(),
            'bank_name'        => $tx->bankAccount?->bank_name,
            'severity'         => $this->deriveSeverity($tx->user->kycProfile?->risk_level),
        ])->toArray();
    }

    private function deriveSeverity(?string $riskLevel): string
    {
        return match ($riskLevel) {
            'high'   => 'high',
            'medium' => 'medium',
            default  => 'low',
        };
    }
}
