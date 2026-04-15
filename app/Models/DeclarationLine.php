<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DeclarationLine extends Model
{
    protected $fillable = [
        'declaration_id',
        'worker_id',
        'gross_salary',
        'contributable_salary',
        'worked_days',
        'anomaly_flag',
        'anomaly_reason',
    ];

    protected function casts(): array
    {
        return [
            'declaration_id' => 'integer',
            'worker_id' => 'integer',
            'gross_salary' => 'decimal:2',
            'contributable_salary' => 'decimal:2',
            'worked_days' => 'integer',
            'anomaly_flag' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function declaration(): BelongsTo
    {
        return $this->belongsTo(Declaration::class);
    }

    public function worker(): BelongsTo
    {
        return $this->belongsTo(Worker::class);
    }

    public function contributionCalc(): HasOne
    {
        return $this->hasOne(ContributionCalc::class);
    }
}
