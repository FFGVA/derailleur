<?php

namespace App\Models;

use App\Enums\EventMemberStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventMember extends Pivot
{
    use Concerns\SetsModifiedBy;
    use SoftDeletes;

    protected $table = 'event_member';

    public $incrementing = true;

    const CREATED_AT = null;

    protected $fillable = [
        'event_id',
        'member_id',
        'status',
        'present',
        'modified_by_id',
    ];

    protected function casts(): array
    {
        return [
            'status' => EventMemberStatus::class,
            'present' => 'boolean',
        ];
    }

    public function modifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'modified_by_id');
    }
}
