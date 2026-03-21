<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberMagicToken extends Model
{
    protected $table = 'member_magic_tokens';

    public $timestamps = false;

    protected $fillable = [
        'member_id',
        'token_hash',
        'expires_at',
        'used_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * @return array{0: static, 1: string} [$model, $rawToken]
     */
    public static function generateFor(Member $member, ?int $expiryMinutes = null): array
    {
        $expiryMinutes ??= config('ffgva.portal_token_expiry_minutes', 15);

        $rawToken = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $rawToken, binary: true);

        $model = static::create([
            'member_id' => $member->id,
            'token_hash' => $tokenHash,
            'expires_at' => now()->addMinutes($expiryMinutes),
        ]);

        return [$model, $rawToken];
    }

    public static function findByRawToken(string $rawToken): ?static
    {
        $tokenHash = hash('sha256', $rawToken, binary: true);

        return static::where('token_hash', $tokenHash)->first();
    }

    public function isValid(): bool
    {
        return !$this->expires_at->isPast() && is_null($this->used_at);
    }

    public function markUsed(): void
    {
        $this->update(['used_at' => now()]);
    }
}
