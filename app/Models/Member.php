<?php

namespace App\Models;

use App\Enums\MemberStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Member extends Model
{
    const CREATED_AT = null;
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'date_of_birth',
        'address',
        'postal_code',
        'city',
        'country',
        'statuscode',
        'membership_start',
        'membership_end',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'statuscode' => MemberStatus::class,
            'date_of_birth' => 'date',
            'membership_start' => 'date',
            'membership_end' => 'date',
        ];
    }

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_member')
            ->withPivot('status', 'updated_at');
    }
}
