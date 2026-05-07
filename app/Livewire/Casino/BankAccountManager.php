<?php

namespace App\Livewire\Casino;

use App\Models\BankAccount;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class BankAccountManager extends Component
{
    public array $accounts = [];
    public bool $showForm = false;

    public string $bankName = '';
    public string $accountNumber = '';
    public string $accountType = 'checking';
    public string $cardholderName = '';

    public string $verifyState = 'idle';
    public string $errorMessage = '';
    public string $successMessage = '';

    public array $banks = [
        'Ameriabank',
        'Ardshinbank',
        'Unibank',
        'ACBA Bank',
        'Evocabank',
        'Converse Bank',
        'Inecobank',
        'Armswissbank',
    ];

    public function mount(): void
    {
        $this->loadAccounts();
        $this->cardholderName = Auth::user()->name;
    }

    public function toggleForm(): void
    {
        $this->showForm = !$this->showForm;
        $this->reset(['bankName', 'accountNumber', 'accountType', 'verifyState', 'errorMessage', 'successMessage']);
        $this->cardholderName = Auth::user()->name;
    }

    public function startVerification(): void
    {
        $this->errorMessage   = '';
        $this->successMessage = '';

        if (empty($this->bankName)) {
            $this->errorMessage = 'Please select a bank.';
            return;
        }

        if (empty($this->accountNumber) || strlen(preg_replace('/\s/', '', $this->accountNumber)) < 8) {
            $this->errorMessage = 'Please enter a valid account number (min 8 characters).';
            return;
        }

        if (empty($this->cardholderName)) {
            $this->errorMessage = 'Cardholder name is required.';
            return;
        }

        $this->verifyState = 'verifying';
    }

    public function completeVerification(): void
    {
        $user = Auth::user();

        $normalizedInput = strtolower(trim($this->cardholderName));
        $normalizedUser  = strtolower(trim($user->name));

        if ($normalizedInput !== $normalizedUser) {
            $this->verifyState  = 'failed';
            $this->errorMessage = 'Cardholder name does not match your account name. Please use the exact name registered on your account.';
            return;
        }

        $cleanNumber = preg_replace('/\s+/', '', $this->accountNumber);

        $exists = $user->bankAccounts()->where('account_number', $cleanNumber)->exists();
        if ($exists) {
            $this->verifyState  = 'failed';
            $this->errorMessage = 'This account number is already registered.';
            return;
        }

        $isPrimary = $user->bankAccounts()->count() === 0;

        $user->bankAccounts()->create([
            'account_number' => $cleanNumber,
            'bank_name'      => $this->bankName,
            'account_type'   => $this->accountType,
            'balance'        => 0,
            'is_primary'     => $isPrimary,
        ]);

        $this->verifyState    = 'verified';
        $this->successMessage = 'Bank account verified and added successfully.';
        $this->loadAccounts();
        $this->dispatch('bankAccountAdded');
    }

    public function setPrimary(int $id): void
    {
        $user = Auth::user();
        $user->bankAccounts()->update(['is_primary' => false]);
        $user->bankAccounts()->where('id', $id)->update(['is_primary' => true]);
        $this->loadAccounts();
    }

    public function removeAccount(int $id): void
    {
        $account = Auth::user()->bankAccounts()->find($id);
        if ($account) {
            $wasPrimary = $account->is_primary;
            $account->delete();

            if ($wasPrimary) {
                $next = Auth::user()->bankAccounts()->first();
                $next?->update(['is_primary' => true]);
            }

            $this->loadAccounts();
        }
    }

    public function resetForm(): void
    {
        $this->reset(['bankName', 'accountNumber', 'accountType', 'verifyState', 'errorMessage', 'successMessage']);
        $this->cardholderName = Auth::user()->name;
        $this->showForm       = true;
    }

    private function loadAccounts(): void
    {
        $this->accounts = Auth::user()
            ->bankAccounts()
            ->orderByDesc('is_primary')
            ->orderBy('created_at')
            ->get()
            ->map(fn($a) => [
                'id'             => $a->id,
                'bank_name'      => $a->bank_name,
                'account_number' => $a->account_number,
                'masked'         => '****' . substr($a->account_number, -4),
                'account_type'   => $a->account_type,
                'is_primary'     => $a->is_primary,
            ])->toArray();
    }

    public function render()
    {
        return view('livewire.casino.bank-account-manager');
    }
}
