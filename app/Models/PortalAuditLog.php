<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortalAuditLog extends Model
{
    protected $table = 'portal_audit_log';

    public $timestamps = false;

    protected $fillable = [
        'member_id',
        'member_number',
        'action',
        'detail',
        'ip_address',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }
}
