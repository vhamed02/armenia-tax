<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KycProfile extends Model
{
    protected $fillable = [
        'user_id',
        'status',
        'annual_income_limit',
        'occupation',
        'employer_name',
        'kyc_verified_at',
        'risk_level',
    ];

    protected function casts(): array
    {
        return [
            'kyc_verified_at'     => 'datetime',
            'annual_income_limit' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
