<?php

namespace App\Models;

use App\Enums\MemberStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\QueryException;

class Member extends Model
{
    use Concerns\SetsModifiedBy;
    use SoftDeletes;

    const CREATED_AT = null;
    const UPDATED_AT = 'updated_at';

    const METADATA_LABELS = [
        'instagram' => 'Instagram',
        'strava' => 'Strava',
        'type_velo' => 'Type de vélo',
        'taille_maillot' => 'Taille maillot',
        'groupe' => 'Groupe',
        'fonction' => 'Fonction',
        'gilet' => 'Gilet',
        'bib' => 'Dossard',
        'aec' => 'AEC',
        'atelier' => 'Atelier',
        'sorties' => 'Sorties',
        'cotisation_ok' => 'Cotisation OK',
        'statuts_ok' => 'Statuts OK',
    ];

    protected static function booted(): void
    {
        static::updating(function (Member $member) {
            if ($member->isDirty('statuscode') && $member->getRawOriginal('statuscode') !== 'A' && $member->statuscode === MemberStatus::Actif && !$member->member_number) {
                $member->member_number = static::nextMemberNumber();
            }
        });
    }

    public static function nextMemberNumber(): string
    {
        $maxNumber = (int) static::max('member_number');

        return str_pad((string) ($maxNumber + 1), 4, '0', STR_PAD_LEFT);
    }

    public function save(array $options = []): bool
    {
        try {
            return parent::save($options);
        } catch (QueryException $e) {
            // Retry once on duplicate member_number (race condition)
            if ($e->errorInfo[1] === 1062 && str_contains($e->getMessage(), 'uk_member_number')) {
                $this->member_number = static::nextMemberNumber();

                return parent::save($options);
            }
            throw $e;
        }
    }

    protected $fillable = [
        'member_number',
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
        'photo_ok',
        'metadata',
        'activation_token',
        'activation_sent_at',
        'email_verified_at',
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
            'photo_ok' => 'boolean',
            'metadata' => 'array',
            'activation_sent_at' => 'datetime',
            'email_verified_at' => 'datetime',
        ];
    }

    /**
     * Assign a member number using high-watermark. Returns the number.
     * If already assigned, returns the existing number.
     */
    public static function assignMemberNumber(Member $member): string
    {
        if ($member->member_number) {
            return $member->member_number;
        }

        $maxNumber = (int) static::max('member_number');
        $next = str_pad((string) ($maxNumber + 1), 4, '0', STR_PAD_LEFT);
        $member->update(['member_number' => $next]);

        return $next;
    }

    public function modifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'modified_by_id');
    }

    public function phones(): HasMany
    {
        return $this->hasMany(MemberPhone::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function ledEvents(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_chef')
            ->whereNull('event_chef.deleted_at');
    }

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_member')
            ->using(EventMember::class)
            ->withPivot('status', 'present', 'updated_at');
    }
}
