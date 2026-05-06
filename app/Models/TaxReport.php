<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaxReport extends Model
{
    protected $fillable = [
        'user_id',
        'report_period_start',
        'report_period_end',
        'total_income',
        'income_limit',
        'excess_income',
        'tax_rate',
        'tax_amount',
        'status',
        'submitted_to_gov_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'report_period_start' => 'date',
            'report_period_end'   => 'date',
            'submitted_to_gov_at' => 'datetime',
            'metadata'            => 'array',
            'tax_rate'            => 'decimal:2',
            'total_income'        => 'integer',
            'income_limit'        => 'integer',
            'excess_income'       => 'integer',
            'tax_amount'          => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
