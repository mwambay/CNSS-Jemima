<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'employer_id',
        'payment_ref',
        'payment_date',
        'amount',
        'channel',
        'status',
        'created_at',
        'validated_at',
    ];

    protected function casts(): array
    {
        return [
            'employer_id' => 'integer',
            'payment_date' => 'date',
            'amount' => 'decimal:2',
            'created_at' => 'datetime',
            'validated_at' => 'datetime',
        ];
    }

    public function employer(): BelongsTo
    {
        return $this->belongsTo(Employer::class);
    }

    public function paymentAllocations(): HasMany
    {
        return $this->hasMany(PaymentAllocation::class);
    }
}
