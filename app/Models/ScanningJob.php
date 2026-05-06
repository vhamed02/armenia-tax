<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScanningJob extends Model
{
    protected $fillable = [
        'user_id',
        'triggered_at',
        'completed_at',
        'status',
        'transactions_scanned',
        'anomalies_found',
    ];

    protected function casts(): array
    {
        return [
            'triggered_at'         => 'datetime',
            'completed_at'         => 'datetime',
            'transactions_scanned' => 'integer',
            'anomalies_found'      => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
