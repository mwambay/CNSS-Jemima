<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CollectionAction extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'case_id',
        'action_type',
        'action_date',
        'result',
        'note',
        'actor_user_id',
    ];

    protected function casts(): array
    {
        return [
            'case_id' => 'integer',
            'actor_user_id' => 'integer',
            'action_date' => 'datetime',
        ];
    }

    public function collectionCase(): BelongsTo
    {
        return $this->belongsTo(CollectionCase::class, 'case_id');
    }

    public function actorUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }
}
