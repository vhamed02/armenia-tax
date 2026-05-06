<?php

namespace App\Jobs;

use App\Models\ScanningJob;
use App\Models\User;
use App\Services\IncomeAnalyzer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Carbon;

class ScanUserTransactions implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly User $user) {}

    public function handle(IncomeAnalyzer $analyzer): void
    {
        $scanningJob = ScanningJob::create([
            'user_id'      => $this->user->id,
            'triggered_at' => Carbon::now(),
            'status'       => 'running',
        ]);

        try {
            $this->user->load(['kycProfile', 'transactions', 'bankAccounts']);

            $analysis = $analyzer->analyzeUser($this->user, 'annual');

            $creditTransactions = $this->user->transactions()
                ->where('transaction_type', 'credit')
                ->whereBetween('transaction_date', [
                    Carbon::now()->startOfYear()->toDateString(),
                    Carbon::now()->endOfYear()->toDateString(),
                ])
                ->get();

            $anomalies     = $analyzer->detectAnomalies($this->user);
            $highAnomalies = array_filter($anomalies, fn($a) => $a['severity'] === 'high');

            if ($analysis['is_over_limit']) {
                $taxReport = $this->user->taxReports()->create([
                    'report_period_start' => Carbon::now()->startOfYear()->toDateString(),
                    'report_period_end'   => Carbon::now()->endOfYear()->toDateString(),
                    'total_income'        => $analysis['total_income'],
                    'income_limit'        => $analysis['income_limit'],
                    'excess_income'       => $analysis['excess_income'],
                    'tax_rate'            => $analysis['tax_breakdown']['total_rate'],
                    'tax_amount'          => $analysis['tax_breakdown']['tax_amount'],
                    'status'              => 'pending',
                    'metadata'            => [
                        'excess_percentage' => $analysis['excess_percentage'],
                        'high_anomalies'    => array_values($highAnomalies),
                    ],
                ]);

                $breach        = $analysis['excess_income'];
                $sorted        = $creditTransactions->sortByDesc('amount');
                $flaggedAmount = 0;

                foreach ($sorted as $tx) {
                    if ($flaggedAmount >= $breach) {
                        break;
                    }
                    $tx->update(['is_flagged' => true]);
                    $flaggedAmount += $tx->amount;
                }

                $this->user->notifications()->create([
                    'type'    => 'limit_exceeded',
                    'title'   => 'Income Limit Exceeded',
                    'message' => sprintf(
                        'Your annual income of %s AMD exceeds your declared limit of %s AMD by %s AMD (%.2f%%).',
                        number_format($analysis['total_income']),
                        number_format($analysis['income_limit']),
                        number_format($analysis['excess_income']),
                        $analysis['excess_percentage']
                    ),
                    'metadata' => ['tax_report_id' => $taxReport->id],
                ]);

                $this->user->notifications()->create([
                    'type'    => 'tax_alert',
                    'title'   => 'Tax Liability Generated',
                    'message' => sprintf(
                        'A tax liability of %s AMD has been calculated at %.2f%% on your excess income of %s AMD.',
                        number_format($analysis['tax_breakdown']['tax_amount']),
                        $analysis['tax_breakdown']['total_rate'],
                        number_format($analysis['excess_income'])
                    ),
                    'metadata' => [
                        'tax_report_id' => $taxReport->id,
                        'tax_breakdown' => $analysis['tax_breakdown'],
                    ],
                ]);
            }

            $scanningJob->update([
                'status'               => 'completed',
                'completed_at'         => Carbon::now(),
                'transactions_scanned' => $creditTransactions->count(),
                'anomalies_found'      => count($highAnomalies),
            ]);
        } catch (\Throwable $e) {
            $scanningJob->update(['status' => 'failed']);
            throw $e;
        }
    }
}
