<?php

namespace App\Models;

use App\Enums\EventStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use SoftDeletes;

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
        'gpx_file',
        'chef_peloton_id',
        'modified_by_id',
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

    public function modifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'modified_by_id');
    }

    public function chefPeloton(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'chef_peloton_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Member::class, 'event_member')
            ->using(EventMember::class)
            ->withPivot('status', 'present', 'updated_at');
    }
}
