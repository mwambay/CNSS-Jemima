<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reminder extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'employer_id',
        'declaration_id',
        'reminder_level',
        'channel',
        'sent_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'employer_id' => 'integer',
            'declaration_id' => 'integer',
            'reminder_level' => 'integer',
            'sent_at' => 'datetime',
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
}
