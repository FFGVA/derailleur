<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MemberStrava extends Model
{
    use SoftDeletes;

    const CREATED_AT = null;

    protected $table = 'member_strava';

    protected $fillable = [
        'member_id',
        'strava_athlete_id',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'scopes',
        'modified_by_id',
    ];

    protected function casts(): array
    {
        return [
            'token_expires_at' => 'datetime',
            'access_token' => 'encrypted',
            'refresh_token' => 'encrypted',
        ];
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
