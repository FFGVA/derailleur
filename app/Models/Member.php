<?php

namespace App\Models;

use App\Enums\MemberStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use SoftDeletes;

    const CREATED_AT = null;
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'date_of_birth',
        'address',
        'postal_code',
        'city',
        'country',
        'statuscode',
        'membership_start',
        'membership_end',
        'notes',
        'is_invitee',
        'metadata',
        'modified_by_id',
    ];

    protected function casts(): array
    {
        return [
            'statuscode' => MemberStatus::class,
            'date_of_birth' => 'date',
            'membership_start' => 'date',
            'membership_end' => 'date',
            'is_invitee' => 'boolean',
            'metadata' => 'array',
        ];
    }

    public function modifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'modified_by_id');
    }

    public function phones(): HasMany
    {
        return $this->hasMany(MemberPhone::class);
    }

    public function ledEvents(): HasMany
    {
        return $this->hasMany(Event::class, 'chef_peloton_id');
    }

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_member')
            ->using(EventMember::class)
            ->withPivot('status', 'present', 'updated_at');
    }
}
