<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class BankAccountSeeder extends Seeder
{
    public function run(): void
    {
        $banks = ['Ameriabank', 'Ardshinbank', 'Unibank', 'ACBA Bank', 'Evocabank'];

        $users = User::where('is_admin', false)->get();

        foreach ($users as $user) {
            $accountCount = random_int(1, 2);
            $usedBanks    = [];

            for ($i = 0; $i < $accountCount; $i++) {
                $bank = $this->pickUnused($banks, $usedBanks);
                $usedBanks[] = $bank;

                $user->bankAccounts()->create([
                    'account_number' => $this->generateAccountNumber(),
                    'bank_name'      => $bank,
                    'account_type'   => $i === 0 ? 'checking' : 'savings',
                    'balance'        => random_int(50000, 2000000),
                    'is_primary'     => $i === 0,
                ]);
            }
        }
    }

    private function pickUnused(array $options, array $used): string
    {
        $available = array_values(array_diff($options, $used));
        return $available[array_rand($available)];
    }

    private function generateAccountNumber(): string
    {
        return 'AM' . str_pad(random_int(1000000000, 9999999999), 10, '0', STR_PAD_LEFT);
    }
}
