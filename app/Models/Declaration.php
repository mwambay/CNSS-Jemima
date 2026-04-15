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
        'total_declared_salary',
        'total_declared_contribution',
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
