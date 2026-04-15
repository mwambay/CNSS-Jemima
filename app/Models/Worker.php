<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Worker extends Model
{
    protected $fillable = [
        'social_security_number',
        'national_id',
        'first_name',
        'last_name',
        'birth_date',
        'gender',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function employments(): HasMany
    {
        return $this->hasMany(Employment::class);
    }

    public function declarationLines(): HasMany
    {
        return $this->hasMany(DeclarationLine::class);
    }

    public function fraudAlerts(): HasMany
    {
        return $this->hasMany(FraudAlert::class);
    }
}
