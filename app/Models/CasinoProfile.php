<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CasinoProfile extends Model
{
    protected $fillable = [
        'user_id',
        'service_provider_id',
        'wallet_balance',
        'kyc_status',
        'kyc_session_token',
        'kyc_verified_at',
        'national_id_verified',
        'casino_username',
    ];

    protected function casts(): array
    {
        return [
            'wallet_balance'  => 'integer',
            'kyc_verified_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function serviceProvider(): BelongsTo
    {
        return $this->belongsTo(ServiceProvider::class);
    }

    public function walletTransactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }
}
