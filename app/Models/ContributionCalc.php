<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContributionCalc extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'declaration_line_id',
        'rate_id',
        'employer_amount',
        'worker_amount',
        'total_amount',
        'calculated_at',
    ];

    protected function casts(): array
    {
        return [
            'declaration_line_id' => 'integer',
            'rate_id' => 'integer',
            'employer_amount' => 'decimal:2',
            'worker_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'calculated_at' => 'datetime',
        ];
    }

    public function declarationLine(): BelongsTo
    {
        return $this->belongsTo(DeclarationLine::class);
    }

    public function rate(): BelongsTo
    {
        return $this->belongsTo(ContributionRate::class, 'rate_id');
    }
}
