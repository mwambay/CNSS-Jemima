<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CollectionCase extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'employer_id',
        'declaration_id',
        'opened_at',
        'status',
        'amount_due',
        'amount_recovered',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'employer_id' => 'integer',
            'declaration_id' => 'integer',
            'opened_at' => 'datetime',
            'amount_due' => 'decimal:2',
            'amount_recovered' => 'decimal:2',
            'closed_at' => 'datetime',
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

    public function collectionActions(): HasMany
    {
        return $this->hasMany(CollectionAction::class, 'case_id');
    }
}
