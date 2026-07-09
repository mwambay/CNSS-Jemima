<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MailDispatch extends Model
{
    public const AFFILIATION_TRACKING = 'AFFILIATION_TRACKING';
    public const AFFILIATION_APPROVED = 'AFFILIATION_APPROVED';
    public const AFFILIATION_REJECTED = 'AFFILIATION_REJECTED';
    public const CONTRIBUTION_REMINDER_D_MINUS_5 = 'CONTRIBUTION_REMINDER_D_MINUS_5';
    public const CONTRIBUTION_REMINDER_D_DAY = 'CONTRIBUTION_REMINDER_D_DAY';

    protected $fillable = [
        'type',
        'recipient_email',
        'recipient_name',
        'emailable_type',
        'emailable_id',
        'sent_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
            'metadata' => 'array',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function emailable(): MorphTo
    {
        return $this->morphTo();
    }
}
