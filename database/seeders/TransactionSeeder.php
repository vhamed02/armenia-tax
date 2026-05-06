<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class TransactionSeeder extends Seeder
{
    private array $creditDescriptions = [
        'Monthly salary',
        'Bank transfer received',
        'Wire transfer',
    ];

    private array $extraCreditDescriptions = [
        'Freelance project payment',
        'Consulting fee',
        'Service income',
    ];

    private array $debitDescriptions = [
        'Utility payment',
        'Grocery purchase',
        'Rent payment',
        'Insurance premium',
        'Mobile top-up',
    ];

    public function run(): void
    {
        $users = User::where('is_admin', false)->with(['kycProfile', 'bankAccounts'])->get();

        $nonCompliantUserIds = $users->sortBy('id')->slice(10, 5)->pluck('id')->toArray();

        foreach ($users as $user) {
            $kyc = $user->kycProfile;
            if (!$kyc) {
                continue;
            }

            $primaryAccount = $user->bankAccounts->firstWhere('is_primary', true)
                ?? $user->bankAccounts->first();

            if (!$primaryAccount) {
                continue;
            }

            $annualLimit    = $kyc->annual_income_limit;
            $monthlyLimit   = (int) ($annualLimit / 12);
            $isNonCompliant = in_array($user->id, $nonCompliantUserIds);

            $baseSalary = random_int(
                (int) ($monthlyLimit * 0.55),
                (int) ($monthlyLimit * 0.80)
            );

            $extraStartMonth = random_int(3, 4);

            for ($month = 1; $month <= 12; $month++) {
                $date = Carbon::now()->startOfYear()->addMonths($month - 1);

                $this->createTransaction(
                    $primaryAccount->id,
                    $user->id,
                    'credit',
                    $baseSalary,
                    'Monthly salary',
                    'salary',
                    $date->copy()->addDays(random_int(1, 5))
                );

                $debitCount = random_int(1, 3);
                for ($d = 0; $d < $debitCount; $d++) {
                    $this->createTransaction(
                        $primaryAccount->id,
                        $user->id,
                        'debit',
                        random_int(5000, 80000),
                        $this->debitDescriptions[array_rand($this->debitDescriptions)],
                        'transfer',
                        $date->copy()->addDays(random_int(6, 25))
                    );
                }

                if ($isNonCompliant && $month >= $extraStartMonth) {
                    $overflowFactor = 1.20 + (mt_rand(0, 40) / 100);
                    $targetAnnual   = (int) ($annualLimit * $overflowFactor);
                    $extraMonths    = 12 - $extraStartMonth + 1;
                    $extraPerMonth  = (int) (($targetAnnual - ($baseSalary * 12)) / $extraMonths);
                    $extraPerMonth  = max($extraPerMonth, 50000);

                    $sourceType  = random_int(0, 1) === 0 ? 'freelance' : 'unknown';
                    $description = $this->extraCreditDescriptions[array_rand($this->extraCreditDescriptions)];

                    $this->createTransaction(
                        $primaryAccount->id,
                        $user->id,
                        'credit',
                        $extraPerMonth,
                        $description,
                        $sourceType,
                        $date->copy()->addDays(random_int(10, 20))
                    );
                }
            }
        }
    }

    private function createTransaction(
        int $bankAccountId,
        int $userId,
        string $type,
        int $amount,
        string $description,
        string $sourceType,
        Carbon $date
    ): void {
        \App\Models\Transaction::create([
            'bank_account_id'    => $bankAccountId,
            'user_id'            => $userId,
            'transaction_type'   => $type,
            'amount'             => $amount,
            'description'        => $description,
            'transaction_date'   => $date->toDateString(),
            'external_reference' => 'REF' . strtoupper(substr(md5(uniqid()), 0, 10)),
            'source_type'        => $sourceType,
            'is_flagged'         => false,
        ]);
    }
}
