<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceLine extends Model
{
    use Concerns\SetsModifiedBy;
    use SoftDeletes;

    const CREATED_AT = null;

    protected $fillable = [
        'invoice_id',
        'description',
        'amount',
        'sort_order',
        'modified_by_id',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function modifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'modified_by_id');
    }
}
