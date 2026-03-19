<?php

namespace App\Models;

use App\Enums\EventStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Event extends Model
{
    const CREATED_AT = null;
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'title',
        'description',
        'location',
        'starts_at',
        'ends_at',
        'max_participants',
        'price',
        'statuscode',
    ];

    protected function casts(): array
    {
        return [
            'statuscode' => EventStatus::class,
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'price' => 'decimal:2',
        ];
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Member::class, 'event_member')
            ->withPivot('status', 'updated_at');
    }
}
