<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Carbon;

class IncomeAnalyzer
{
    public function analyzeUser(User $user, string $period = 'annual'): array
    {
        $kyc = $user->kycProfile;

        [$start, $end] = $this->periodBounds($period);

        $credits = $user->transactions()
            ->where('transaction_type', 'credit')
            ->whereBetween('transaction_date', [$start, $end])
            ->get();

        $totalIncome  = (int) $credits->sum('amount');
        $annualLimit  = (int) $kyc->annual_income_limit;
        $incomeLimit  = $period === 'monthly' ? (int) round($annualLimit / 12) : $annualLimit;
        $excessIncome = max(0, $totalIncome - $incomeLimit);
        $isOverLimit  = $excessIncome > 0;

        $excessPercentage = $incomeLimit > 0
            ? round(($excessIncome / $incomeLimit) * 100, 2)
            : 0.0;

        $hasSurcharge = $incomeLimit > 0 && $excessIncome > ($incomeLimit * 0.5);

        return [
            'total_income'      => $totalIncome,
            'income_limit'      => $incomeLimit,
            'excess_income'     => $excessIncome,
            'excess_percentage' => $excessPercentage,
            'is_over_limit'     => $isOverLimit,
            'period'            => $period,
            'tax_breakdown'     => $this->calculateTax($excessIncome, $kyc->risk_level, $hasSurcharge),
        ];
    }

    public function calculateTax(int $excessIncome, string $riskLevel, bool $applySurcharge = false): array
    {
        $baseRates = [
            'low'    => 10.0,
            'medium' => 15.0,
            'high'   => 20.0,
        ];

        $baseRate      = $baseRates[$riskLevel] ?? 10.0;
        $surchargeRate = $applySurcharge ? 5.0 : 0.0;
        $totalRate     = $baseRate + $surchargeRate;
        $taxAmount     = (int) round($excessIncome * ($totalRate / 100));

        return [
            'base_rate'      => $baseRate,
            'surcharge_rate' => $surchargeRate,
            'total_rate'     => $totalRate,
            'tax_amount'     => $taxAmount,
        ];
    }

    public function detectAnomalies(User $user): array
    {
        $kyc          = $user->kycProfile;
        $monthlyLimit = (int) round($kyc->annual_income_limit / 12);
        $anomalies    = [];

        $largeCredits = $user->transactions()
            ->where('transaction_type', 'credit')
            ->where('amount', '>', (int) round($monthlyLimit * 0.30))
            ->get();

        if ($largeCredits->isNotEmpty()) {
            $anomalies[] = [
                'type'            => 'large_single_credit',
                'description'     => 'One or more credit transactions exceed 30% of the monthly income limit.',
                'severity'        => 'high',
                'transaction_ids' => $largeCredits->pluck('id')->toArray(),
            ];
        }

        $unknownCredits = $user->transactions()
            ->where('transaction_type', 'credit')
            ->where('source_type', 'unknown')
            ->get();

        $weeklyGroups    = $unknownCredits->groupBy(fn($tx) => Carbon::parse($tx->transaction_date)->format('o-W'));
        $suspiciousWeeks = $weeklyGroups->filter(fn($group) => $group->count() > 3);

        if ($suspiciousWeeks->isNotEmpty()) {
            $anomalies[] = [
                'type'            => 'clustered_unknown_sources',
                'description'     => 'More than 3 transactions with unknown source type detected within the same week.',
                'severity'        => 'high',
                'transaction_ids' => $suspiciousWeeks->flatten()->pluck('id')->toArray(),
            ];
        }

        $allCredits = $user->transactions()
            ->where('transaction_type', 'credit')
            ->get();

        $monthlySourceTypes = $allCredits->groupBy(fn($tx) => Carbon::parse($tx->transaction_date)->format('Y-m'));
        $diverseMonths      = $monthlySourceTypes->filter(fn($group) => $group->pluck('source_type')->unique()->count() > 3);

        if ($diverseMonths->isNotEmpty()) {
            $anomalies[] = [
                'type'            => 'diverse_income_sources',
                'description'     => 'Credits from more than 3 different source types detected within a single month.',
                'severity'        => 'medium',
                'transaction_ids' => $diverseMonths->flatten()->pluck('id')->toArray(),
            ];
        }

        return $anomalies;
    }

    private function periodBounds(string $period): array
    {
        if ($period === 'monthly') {
            return [
                Carbon::now()->startOfMonth()->toDateString(),
                Carbon::now()->endOfMonth()->toDateString(),
            ];
        }

        return [
            Carbon::now()->startOfYear()->toDateString(),
            Carbon::now()->endOfYear()->toDateString(),
        ];
    }
}
