<?php

namespace App\Models;

use App\Enums\EventMemberStatus;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventMember extends Pivot
{
    use SoftDeletes;

    protected $table = 'event_member';

    public $incrementing = true;

    const CREATED_AT = null;

    protected function casts(): array
    {
        return [
            'status' => EventMemberStatus::class,
            'present' => 'boolean',
        ];
    }
}
