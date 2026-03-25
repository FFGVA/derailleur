<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventChef extends Model
{
    use Concerns\SetsModifiedBy;
    use SoftDeletes;

    const CREATED_AT = null;

    protected $table = 'event_chef';

    protected $fillable = [
        'event_id',
        'member_id',
        'sort_order',
        'modified_by_id',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function modifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'modified_by_id');
    }
}
