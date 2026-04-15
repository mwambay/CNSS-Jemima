<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FraudAlert extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'rule_id',
        'employer_id',
        'worker_id',
        'declaration_id',
        'score',
        'status',
        'details',
        'detected_at',
        'assigned_user_id',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'rule_id' => 'integer',
            'employer_id' => 'integer',
            'worker_id' => 'integer',
            'declaration_id' => 'integer',
            'assigned_user_id' => 'integer',
            'score' => 'decimal:2',
            'details' => 'array',
            'detected_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    public function rule(): BelongsTo
    {
        return $this->belongsTo(FraudRule::class, 'rule_id');
    }

    public function employer(): BelongsTo
    {
        return $this->belongsTo(Employer::class);
    }

    public function worker(): BelongsTo
    {
        return $this->belongsTo(Worker::class);
    }

    public function declaration(): BelongsTo
    {
        return $this->belongsTo(Declaration::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }
}
