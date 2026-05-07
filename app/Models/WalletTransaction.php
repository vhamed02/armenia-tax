<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletTransaction extends Model
{
    protected $fillable = [
        'casino_profile_id',
        'service_provider_id',
        'user_id',
        'type',
        'amount',
        'status',
        'bank_account_id',
        'balance_before',
        'balance_after',
        'external_reference',
        'failure_reason',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'amount'         => 'integer',
            'balance_before' => 'integer',
            'balance_after'  => 'integer',
            'metadata'       => 'array',
        ];
    }

    public function casinoProfile(): BelongsTo
    {
        return $this->belongsTo(CasinoProfile::class);
    }

    public function serviceProvider(): BelongsTo
    {
        return $this->belongsTo(ServiceProvider::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }
}
