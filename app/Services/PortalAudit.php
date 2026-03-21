<?php

namespace App\Services;

use App\Models\Member;
use App\Models\PortalAuditLog;
use Illuminate\Http\Request;

class PortalAudit
{
    public static function log(Request $request, Member $member, string $action, ?string $detail = null): void
    {
        PortalAuditLog::create([
            'member_id' => $member->id,
            'member_number' => $member->member_number,
            'action' => $action,
            'detail' => $detail,
            'ip_address' => $request->ip(),
        ]);
    }
}
