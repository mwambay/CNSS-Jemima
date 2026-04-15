<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentAllocation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'payment_id',
        'declaration_id',
        'allocated_amount',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'payment_id' => 'integer',
            'declaration_id' => 'integer',
            'allocated_amount' => 'decimal:2',
            'created_at' => 'datetime',
        ];
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function declaration(): BelongsTo
    {
        return $this->belongsTo(Declaration::class);
    }
}
