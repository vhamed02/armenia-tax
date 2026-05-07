<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceProvider extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'website',
        'logo_url',
        'api_key',
        'api_secret',
        'status',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'settings' => 'array',
        ];
    }

    public function casinoProfiles(): HasMany
    {
        return $this->hasMany(CasinoProfile::class);
    }

    public function walletTransactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
