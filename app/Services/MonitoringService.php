<?php

namespace App\Services;

use App\Models\CasinoProfile;
use App\Models\ServiceProvider;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

class MonitoringService
{
    public function __construct(private readonly IncomeAnalyzer $analyzer) {}

    public function getTenantOverview(): array
    {
        $providers = ServiceProvider::all();

        return $providers->map(function ($provider) {
            $profiles = CasinoProfile::where('service_provider_id', $provider->id)->with('user.kycProfile')->get();

            $totalUsers    = $profiles->count();
            $verifiedUsers = $profiles->where('kyc_status', 'verified')->count();

            $walletTxs = WalletTransaction::where('service_provider_id', $provider->id)
                ->where('status', 'completed')
                ->get();

            $totalDeposits    = (int) $walletTxs->where('type', 'deposit')->sum('amount');
            $totalWithdrawals = (int) $walletTxs->where('type', 'withdrawal')->sum('amount');

            $usersOverLimit = 0;
            $totalTaxDue    = 0;

            foreach ($profiles->where('kyc_status', 'verified') as $profile) {
                $user = $profile->user;
                if (!$user || !$user->kycProfile) {
                    continue;
                }
                $analysis = $this->analyzer->analyzeUser($user, 'annual');
                if ($analysis['is_over_limit']) {
                    $usersOverLimit++;
                    $totalTaxDue += $analysis['tax_breakdown']['tax_amount'];
                }
            }

            $lastTx = WalletTransaction::where('service_provider_id', $provider->id)
                ->orderByDesc('created_at')
                ->first();

            return [
                'id'                  => $provider->id,
                'provider_name'       => $provider->name,
                'slug'                => $provider->slug,
                'status'              => $provider->status,
                'total_users'         => $totalUsers,
                'verified_users'      => $verifiedUsers,
                'total_deposits_amd'  => $totalDeposits,
                'total_withdrawals_amd' => $totalWithdrawals,
                'users_over_limit'    => $usersOverLimit,
                'total_tax_due_amd'   => $totalTaxDue,
                'last_activity_at'    => $lastTx?->created_at?->diffForHumans(),
            ];
        })->toArray();
    }

    public function getTransactionLog(array $filters = []): LengthAwarePaginator
    {
        $query = WalletTransaction::with([
            'serviceProvider',
            'user.kycProfile',
            'casinoProfile',
        ])->orderByDesc('created_at');

        if (!empty($filters['service_provider_id'])) {
            $query->where('service_provider_id', $filters['service_provider_id']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('user', fn($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('national_id', 'like', "%{$search}%")
            );
        }

        return $query->paginate(25)->through(function ($tx) {
            $user    = $tx->user;
            $kyc     = $user?->kycProfile;
            $profile = $tx->casinoProfile;

            $annualLimit  = $kyc ? (int) $kyc->annual_income_limit : 0;
            $currentIncome = 0;

            if ($user && $kyc) {
                $analysis      = $this->analyzer->analyzeUser($user, 'annual');
                $currentIncome = $analysis['total_income'];
            }

            $remaining   = max(0, $annualLimit - $currentIncome);
            $isOverLimit = $currentIncome > $annualLimit && $annualLimit > 0;
            $excess      = $isOverLimit ? ($currentIncome - $annualLimit) : 0;

            return [
                'transaction_id'           => $tx->id,
                'datetime'                 => $tx->created_at->format('Y-m-d H:i'),
                'service_provider_name'    => $tx->serviceProvider?->name ?? '—',
                'service_provider_id'      => $tx->service_provider_id,
                'user_id'                  => $tx->user_id,
                'user_name'                => $user?->name ?? '—',
                'national_id'              => $user?->national_id ?? '—',
                'transaction_type'         => $tx->type,
                'amount_amd'               => $tx->amount,
                'wallet_balance_after'     => $tx->balance_after,
                'user_annual_limit'        => $annualLimit,
                'user_current_annual_income' => $currentIncome,
                'remaining_limit'          => $remaining,
                'is_over_limit'            => $isOverLimit,
                'excess_amount'            => $excess,
                'kyc_status'               => $profile?->kyc_status ?? 'unknown',
                'status'                   => $tx->status,
            ];
        });
    }

    public function getUserIncomeStatus(int $userId): array
    {
        $user = User::with(['kycProfile', 'casinoProfiles.serviceProvider'])->findOrFail($userId);
        $kyc  = $user->kycProfile;

        $analysis = $kyc ? $this->analyzer->analyzeUser($user, 'annual') : null;

        $annualLimit   = $kyc ? (int) $kyc->annual_income_limit : 0;
        $currentIncome = $analysis ? $analysis['total_income'] : 0;
        $remaining     = max(0, $annualLimit - $currentIncome);
        $isOverLimit   = $analysis ? $analysis['is_over_limit'] : false;
        $excess        = $isOverLimit ? ($currentIncome - $annualLimit) : 0;
        $estimatedTax  = $analysis ? $analysis['tax_breakdown']['tax_amount'] : 0;

        $walletCount = WalletTransaction::where('user_id', $userId)->count();

        $activeProviders = $user->casinoProfiles
            ->where('kyc_status', 'verified')
            ->map(fn($p) => $p->serviceProvider?->name)
            ->filter()
            ->values()
            ->toArray();

        return [
            'user_name'                => $user->name,
            'national_id'              => $user->national_id,
            'kyc_status'               => $kyc?->status ?? 'none',
            'risk_level'               => $kyc?->risk_level ?? 'unknown',
            'annual_income_limit'      => $annualLimit,
            'current_year_income'      => $currentIncome,
            'remaining_limit'          => $remaining,
            'is_over_limit'            => $isOverLimit,
            'excess_amount'            => $excess,
            'estimated_tax'            => $estimatedTax,
            'wallet_transactions_count'=> $walletCount,
            'active_on_providers'      => $activeProviders,
        ];
    }

    public function toggleProviderStatus(int $providerId): ServiceProvider
    {
        $provider = ServiceProvider::findOrFail($providerId);
        $provider->update([
            'status' => $provider->status === 'active' ? 'suspended' : 'active',
        ]);
        return $provider->fresh();
    }

    public function getDashboardTenantStats(): array
    {
        $today = Carbon::now()->startOfDay();

        $activeProviders = ServiceProvider::where('status', 'active')->count();

        $walletToday = WalletTransaction::where('created_at', '>=', $today)->count();

        $totalVolume = (int) WalletTransaction::where('status', 'completed')
            ->where('type', 'deposit')
            ->sum('amount');

        $crossTenantOverLimit = User::where('is_admin', false)
            ->whereHas('casinoProfiles', fn($q) => $q->where('kyc_status', 'verified'))
            ->whereHas('taxReports')
            ->count();

        $topProviders = ServiceProvider::withCount([
            'walletTransactions as today_count' => fn($q) => $q->where('created_at', '>=', $today),
        ])
            ->orderByDesc('today_count')
            ->limit(5)
            ->get()
            ->map(fn($p) => [
                'name'        => $p->name,
                'slug'        => $p->slug,
                'today_count' => $p->today_count,
                'status'      => $p->status,
            ])->toArray();

        return [
            'active_service_providers'     => $activeProviders,
            'casino_wallet_txs_today'      => $walletToday,
            'total_wallet_volume_amd'      => $totalVolume,
            'cross_platform_users_over_limit' => $crossTenantOverLimit,
            'top_providers_today'          => $topProviders,
        ];
    }
}
