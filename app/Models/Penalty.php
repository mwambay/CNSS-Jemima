<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Penalty extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'employer_id',
        'declaration_id',
        'penalty_type',
        'base_amount',
        'penalty_rate',
        'days_late',
        'amount',
        'status',
        'assessed_at',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'employer_id' => 'integer',
            'declaration_id' => 'integer',
            'base_amount' => 'decimal:2',
            'penalty_rate' => 'decimal:4',
            'days_late' => 'integer',
            'amount' => 'decimal:2',
            'assessed_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    public function employer(): BelongsTo
    {
        return $this->belongsTo(Employer::class);
    }

    public function declaration(): BelongsTo
    {
        return $this->belongsTo(Declaration::class);
    }
}
