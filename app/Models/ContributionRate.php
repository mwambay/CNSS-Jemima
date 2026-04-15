<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContributionRate extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'regime_code',
        'effective_from',
        'effective_to',
        'employer_rate',
        'worker_rate',
        'ceiling_amount',
        'floor_amount',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'effective_from' => 'date',
            'effective_to' => 'date',
            'employer_rate' => 'decimal:4',
            'worker_rate' => 'decimal:4',
            'ceiling_amount' => 'decimal:2',
            'floor_amount' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function contributionCalcs(): HasMany
    {
        return $this->hasMany(ContributionCalc::class, 'rate_id');
    }
}
