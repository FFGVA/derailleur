<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MemberPhone extends Model
{
    use SoftDeletes;

    const CREATED_AT = null;

    protected $fillable = [
        'member_id',
        'phone_number',
        'label',
        'is_whatsapp',
        'sort_order',
        'modified_by_id',
    ];

    protected function casts(): array
    {
        return [
            'is_whatsapp' => 'boolean',
        ];
    }

    public function modifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'modified_by_id');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
