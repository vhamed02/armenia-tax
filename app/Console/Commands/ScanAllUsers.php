<?php

namespace App\Console\Commands;

use App\Jobs\ScanUserTransactions;
use App\Models\User;
use Illuminate\Console\Command;

class ScanAllUsers extends Command
{
    protected $signature = 'arfims:scan-all';

    protected $description = 'Dispatch transaction scanning jobs for all KYC-verified users';

    public function handle(): int
    {
        $users = User::whereHas('kycProfile', fn($q) => $q->where('status', 'verified'))->get();

        if ($users->isEmpty()) {
            $this->warn('No verified users found.');
            return self::SUCCESS;
        }

        $this->info("Dispatching scan jobs for {$users->count()} verified users...");

        $bar = $this->output->createProgressBar($users->count());
        $bar->start();

        foreach ($users as $user) {
            ScanUserTransactions::dispatch($user);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('All scan jobs dispatched successfully.');

        return self::SUCCESS;
    }
}
