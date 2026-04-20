<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Services\InvoicePdfService;
use Illuminate\Http\Request;

class FactureController extends Controller
{
    public function factures(Request $request)
    {
        $member = $request->attributes->get('portal_member');
        $invoices = $member->invoices()
            ->whereNull('deleted_at')
            ->whereIn('statuscode', [InvoiceStatus::New->value, InvoiceStatus::Sent->value, InvoiceStatus::Paid->value])
            ->orderByDesc('updated_at')
            ->with('lines')
            ->get();

        return view('portail.factures', [
            'member' => $member,
            'invoices' => $invoices,
        ]);
    }

    public function facturePdf(Request $request, Invoice $invoice)
    {
        $member = $request->attributes->get('portal_member');

        if ($invoice->member_id !== $member->id) {
            abort(403);
        }

        $filename = $invoice->pdf_filename;

        // Try to find existing file
        if ($filename) {
            $path = storage_path('app/private/invoices/' . $filename);
            if (!file_exists($path)) {
                $path = storage_path('app/invoices/' . $filename);
            }
            if (file_exists($path)) {
                return response()->file($path, ['Content-Type' => 'application/pdf']);
            }
        }

        // Generate on the fly if missing
        $result = InvoicePdfService::generate($invoice);

        return response($result['pdf'], 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $result['filename'] . '"',
        ]);
    }
}
