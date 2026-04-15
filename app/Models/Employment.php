<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employment extends Model
{
    protected $fillable = [
        'employer_id',
        'worker_id',
        'contract_type',
        'start_date',
        'end_date',
        'base_salary',
        'is_declared_active',
    ];

    protected function casts(): array
    {
        return [
            'employer_id' => 'integer',
            'worker_id' => 'integer',
            'start_date' => 'date',
            'end_date' => 'date',
            'base_salary' => 'decimal:2',
            'is_declared_active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function employer(): BelongsTo
    {
        return $this->belongsTo(Employer::class);
    }

    public function worker(): BelongsTo
    {
        return $this->belongsTo(Worker::class);
    }
}
