<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;

    const CREATED_AT = null;

    protected $fillable = [
        'member_id',
        'type',
        'cotisation_year',
        'invoice_number',
        'amount',
        'statuscode',
        'payment_date',
        'notes',
        'modified_by_id',
    ];

    protected function casts(): array
    {
        return [
            'type' => InvoiceType::class,
            'statuscode' => InvoiceStatus::class,
            'amount' => 'decimal:2',
            'cotisation_year' => 'integer',
            'payment_date' => 'date',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'invoice_event');
    }

    public function modifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'modified_by_id');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(InvoiceLine::class)->orderBy('sort_order');
    }

    public function recalculateAmount(): void
    {
        $this->update(['amount' => $this->lines()->sum('amount')]);
    }

    public static function generateNumber(Member $member): string
    {
        $year = date('Y');
        $memberId = str_pad((string) $member->id, 3, '0', STR_PAD_LEFT);
        $prefix = "{$year}-{$memberId}-";

        $last = static::where('invoice_number', 'like', "{$prefix}%")
            ->orderByDesc('invoice_number')
            ->value('invoice_number');

        if ($last) {
            $seq = (int) substr($last, -3) + 1;
        } else {
            $seq = 1;
        }

        return $prefix . str_pad((string) $seq, 3, '0', STR_PAD_LEFT);
    }
}
