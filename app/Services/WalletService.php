<?php

namespace App\Services;

use App\Jobs\ScanUserTransactions;
use App\Models\BankAccount;
use App\Models\CasinoProfile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class WalletService
{
    public function deposit(CasinoProfile $profile, int $amountAmd, array $meta = []): array
    {
        if ($profile->kyc_status !== 'verified') {
            return ['success' => false, 'error' => 'KYC verification required before depositing.'];
        }

        if ($amountAmd <= 0) {
            return ['success' => false, 'error' => 'Deposit amount must be greater than zero.'];
        }

        if ($amountAmd > 5000000) {
            return ['success' => false, 'error' => 'Maximum single deposit is 5,000,000 AMD.'];
        }

        return DB::transaction(function () use ($profile, $amountAmd, $meta) {
            $profile->refresh();
            $balanceBefore = $profile->wallet_balance;
            $balanceAfter  = $balanceBefore + $amountAmd;

            $profile->update(['wallet_balance' => $balanceAfter]);

            $walletTx = $profile->walletTransactions()->create([
                'service_provider_id' => $profile->service_provider_id,
                'user_id'             => $profile->user_id,
                'type'                => 'deposit',
                'amount'              => $amountAmd,
                'status'              => 'completed',
                'balance_before'      => $balanceBefore,
                'balance_after'       => $balanceAfter,
                'metadata'            => $meta ?: null,
            ]);

            $provider = $profile->serviceProvider;

            $profile->user->transactions()->create([
                'bank_account_id'     => null,
                'service_provider_id' => $profile->service_provider_id,
                'transaction_type'    => 'credit',
                'amount'              => $amountAmd,
                'source_type'         => 'transfer',
                'description'         => 'Casino deposit — ' . $provider->name,
                'transaction_date'    => Carbon::today()->toDateString(),
                'external_reference'  => (string) $walletTx->id,
                'is_flagged'          => false,
            ]);

            ScanUserTransactions::dispatch($profile->user);

            return [
                'success'        => true,
                'new_balance'    => $balanceAfter,
                'transaction_id' => $walletTx->id,
            ];
        });
    }

    public function withdraw(CasinoProfile $profile, int $amountAmd, BankAccount $bankAccount): array
    {
        if ($profile->kyc_status !== 'verified') {
            return ['success' => false, 'error' => 'KYC verification required before withdrawing.'];
        }

        if ($amountAmd <= 0) {
            return ['success' => false, 'error' => 'Withdrawal amount must be greater than zero.'];
        }

        if ($profile->wallet_balance < $amountAmd) {
            return ['success' => false, 'error' => 'Insufficient wallet balance.'];
        }

        return DB::transaction(function () use ($profile, $amountAmd, $bankAccount) {
            $profile->refresh();

            if ($profile->wallet_balance < $amountAmd) {
                return ['success' => false, 'error' => 'Insufficient wallet balance.'];
            }

            $balanceBefore = $profile->wallet_balance;
            $balanceAfter  = $balanceBefore - $amountAmd;

            $profile->update(['wallet_balance' => $balanceAfter]);

            $maskedAccount = '****' . substr($bankAccount->account_number, -4);

            $walletTx = $profile->walletTransactions()->create([
                'service_provider_id' => $profile->service_provider_id,
                'user_id'             => $profile->user_id,
                'type'                => 'withdrawal',
                'amount'              => $amountAmd,
                'status'              => 'completed',
                'bank_account_id'     => $bankAccount->id,
                'balance_before'      => $balanceBefore,
                'balance_after'       => $balanceAfter,
            ]);

            $profile->user->transactions()->create([
                'bank_account_id'     => $bankAccount->id,
                'service_provider_id' => $profile->service_provider_id,
                'transaction_type'    => 'debit',
                'amount'              => $amountAmd,
                'source_type'         => 'transfer',
                'description'         => 'Casino withdrawal to ' . $bankAccount->bank_name . ' ' . $maskedAccount,
                'transaction_date'    => Carbon::today()->toDateString(),
                'external_reference'  => (string) $walletTx->id,
                'is_flagged'          => false,
            ]);

            return [
                'success'        => true,
                'new_balance'    => $balanceAfter,
                'transaction_id' => $walletTx->id,
            ];
        });
    }

    public function getWalletSummary(CasinoProfile $profile): array
    {
        $txs = $profile->walletTransactions()->where('status', 'completed')->get();

        $lastTx = $profile->walletTransactions()->orderByDesc('created_at')->first();

        return [
            'current_balance'    => $profile->wallet_balance,
            'total_deposited'    => (int) $txs->where('type', 'deposit')->sum('amount'),
            'total_withdrawn'    => (int) $txs->where('type', 'withdrawal')->sum('amount'),
            'transaction_count'  => $txs->count(),
            'last_transaction_at'=> $lastTx?->created_at?->toDateTimeString(),
        ];
    }
}
