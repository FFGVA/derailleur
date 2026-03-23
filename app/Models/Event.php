<?php

namespace App\Models;

use App\Enums\EventStatus;
use App\Enums\EventType;
use App\Enums\MemberStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use SoftDeletes;

    const CREATED_AT = null;
    const UPDATED_AT = 'updated_at';

    protected static function booted(): void
    {
        // Sync chef_peloton_id → event_chef pivot for backward compatibility
        static::saved(function (Event $event) {
            if ($event->chef_peloton_id && !$event->eventChefs()->where('member_id', $event->chef_peloton_id)->exists()) {
                EventChef::create([
                    'event_id' => $event->id,
                    'member_id' => $event->chef_peloton_id,
                    'sort_order' => 0,
                ]);
            }
        });
    }

    protected $fillable = [
        'event_type',
        'title',
        'description',
        'location',
        'starts_at',
        'ends_at',
        'max_participants',
        'price',
        'price_non_member',
        'statuscode',
        'strava_event_id',
        'strava_route_id',
        'gpx_file',
        'chef_peloton_id',
        'modified_by_id',
    ];

    protected function casts(): array
    {
        return [
            'event_type' => EventType::class,
            'statuscode' => EventStatus::class,
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'price' => 'decimal:2',
            'price_non_member' => 'decimal:2',
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

    /**
     * Get the applicable price for a member: active members pay price, others pay price_non_member (fallback to price).
     */
    public function priceForMember(Member $member): string
    {
        if ($member->statuscode === MemberStatus::Actif) {
            return $this->price;
        }

        return $this->price_non_member ?? $this->price;
    }

    public function isFull(): bool
    {
        if (is_null($this->max_participants)) {
            return false;
        }

        return $this->members()
            ->whereIn('event_member.status', ['N', 'C'])
            ->whereNull('event_member.deleted_at')
            ->count() >= $this->max_participants;
    }

    public function chefs(): BelongsToMany
    {
        return $this->belongsToMany(Member::class, 'event_chef')
            ->whereNull('event_chef.deleted_at')
            ->withPivot('sort_order')
            ->orderByPivot('sort_order');
    }

    public function eventChefs(): HasMany
    {
        return $this->hasMany(EventChef::class);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Member::class, 'event_member')
            ->using(EventMember::class)
            ->withPivot('status', 'present', 'updated_at');
    }
}
