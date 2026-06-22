<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Declaration extends Model
{
    protected $fillable = [
        'employer_id',
        'period_year',
        'period_month',
        'submitted_at',
        'due_date',
        'status',
        'contribution_entry_mode',
        'total_declared_salary',
        'total_declared_contribution',
        'global_contribution_amount',
        'global_amount_due',
        'global_contribution_date',
        'global_late_days',
        'global_late_penalty_rate',
        'global_late_penalty_amount',
        'global_total_payable',
        'global_salary_envelope',
        'global_employer_rate',
        'global_worker_rate',
        'global_worker_count',
        'validation_message',
    ];

    protected function casts(): array
    {
        return [
            'employer_id' => 'integer',
            'period_year' => 'integer',
            'period_month' => 'integer',
            'submitted_at' => 'datetime',
            'due_date' => 'date',
            'total_declared_salary' => 'decimal:2',
            'total_declared_contribution' => 'decimal:2',
            'global_contribution_amount' => 'decimal:2',
            'global_amount_due' => 'decimal:2',
            'global_contribution_date' => 'date',
            'global_late_days' => 'integer',
            'global_late_penalty_rate' => 'decimal:4',
            'global_late_penalty_amount' => 'decimal:2',
            'global_total_payable' => 'decimal:2',
            'global_salary_envelope' => 'decimal:2',
            'global_employer_rate' => 'decimal:4',
            'global_worker_rate' => 'decimal:4',
            'global_worker_count' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function employer(): BelongsTo
    {
        return $this->belongsTo(Employer::class);
    }

    public function declarationLines(): HasMany
    {
        return $this->hasMany(DeclarationLine::class);
    }

    public function penalties(): HasMany
    {
        return $this->hasMany(Penalty::class);
    }

    public function paymentAllocations(): HasMany
    {
        return $this->hasMany(PaymentAllocation::class);
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(Reminder::class);
    }

    public function collectionCases(): HasMany
    {
        return $this->hasMany(CollectionCase::class);
    }

    public function fraudAlerts(): HasMany
    {
        return $this->hasMany(FraudAlert::class);
    }
}
