<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Invoice;
use App\Models\InvoiceLine;
use App\Models\Member;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $members = Member::all();
        $events = Event::all();

        if ($members->isEmpty()) {
            return;
        }

        // Set membership_end dates for expiry testing:
        // 2 members: expired last month
        // 3 members: expiring this month
        // 2 members: expiring next month
        // 2 members: expiring month after next
        // rest: 6+ months out
        $expiryDates = [
            now()->subMonth()->startOfMonth()->addDays(10),    // last month
            now()->subMonth()->startOfMonth()->addDays(20),    // last month
            now()->startOfMonth()->addDays(5),                  // this month
            now()->startOfMonth()->addDays(15),                 // this month
            now()->startOfMonth()->addDays(25),                 // this month
            now()->addMonth()->startOfMonth()->addDays(10),     // next month
            now()->addMonth()->startOfMonth()->addDays(20),     // next month
            now()->addMonths(2)->startOfMonth()->addDays(15),   // month after next
            now()->addMonths(2)->startOfMonth()->addDays(25),   // month after next
        ];

        foreach ($members->take(count($expiryDates)) as $i => $member) {
            $member->update([
                'statuscode' => 'A',
                'membership_start' => $expiryDates[$i]->copy()->subYear(),
                'membership_end' => $expiryDates[$i],
            ]);
        }

        // Remaining members: membership far in the future
        foreach ($members->skip(count($expiryDates)) as $member) {
            $member->update([
                'statuscode' => 'A',
                'membership_start' => now()->subMonths(rand(1, 6)),
                'membership_end' => now()->addMonths(rand(6, 11)),
            ]);
        }

        // --- Type C: Cotisation invoices with lines ---
        foreach ($members->take(10) as $i => $member) {
            $start = $member->membership_start?->format('d.m.Y') ?? now()->subYear()->format('d.m.Y');
            $end = $member->membership_end?->format('d.m.Y') ?? now()->format('d.m.Y');
            $isPaid = $i < 6;

            $invoice = Invoice::create([
                'member_id' => $member->id,
                'type' => 'C',
                'cotisation_year' => $member->membership_end?->year ?? now()->year,
                'invoice_number' => Invoice::generateNumber($member),
                'amount' => 50.00,
                'statuscode' => $isPaid ? 'P' : ($i < 8 ? 'E' : 'N'),
                'payment_date' => $isPaid ? now()->subDays(rand(10, 90)) : null,
            ]);

            InvoiceLine::create([
                'invoice_id' => $invoice->id,
                'description' => "Période d'adhésion du {$start} au {$end}",
                'amount' => 50.00,
                'sort_order' => 0,
            ]);
        }

        // --- Type E: Événement invoices with lines ---
        foreach ($members->take(4) as $i => $member) {
            $event = $events->get($i % $events->count());
            if (!$event) continue;

            $invoice = Invoice::create([
                'member_id' => $member->id,
                'type' => 'E',
                'invoice_number' => Invoice::generateNumber($member),
                'amount' => $event->price,
                'statuscode' => $i < 2 ? 'P' : 'N',
                'payment_date' => $i < 2 ? now()->subDays(rand(5, 30)) : null,
            ]);

            $invoice->events()->attach($event->id);

            InvoiceLine::create([
                'invoice_id' => $invoice->id,
                'description' => $event->title . ' — ' . $event->starts_at->format('d.m.Y'),
                'amount' => $event->price,
                'sort_order' => 0,
            ]);
        }

        // --- Type A: Autre invoices with multiple lines ---
        $member = $members->get(5) ?? $members->first();
        $invoice = Invoice::create([
            'member_id' => $member->id,
            'type' => 'A',
            'invoice_number' => Invoice::generateNumber($member),
            'amount' => 45.00,
            'statuscode' => 'N',
            'notes' => 'Commande équipement',
        ]);

        InvoiceLine::create([
            'invoice_id' => $invoice->id,
            'description' => 'Maillot FFGVA taille M',
            'amount' => 35.00,
            'sort_order' => 0,
        ]);
        InvoiceLine::create([
            'invoice_id' => $invoice->id,
            'description' => 'Bidon FFGVA',
            'amount' => 10.00,
            'sort_order' => 1,
        ]);

        // Another "autre" invoice
        $member = $members->get(8) ?? $members->first();
        $invoice = Invoice::create([
            'member_id' => $member->id,
            'type' => 'A',
            'invoice_number' => Invoice::generateNumber($member),
            'amount' => 20.00,
            'statuscode' => 'P',
            'payment_date' => now()->subDays(15),
            'notes' => 'Casquette et autocollant',
        ]);

        InvoiceLine::create([
            'invoice_id' => $invoice->id,
            'description' => 'Casquette FFGVA',
            'amount' => 15.00,
            'sort_order' => 0,
        ]);
        InvoiceLine::create([
            'invoice_id' => $invoice->id,
            'description' => 'Autocollant FFGVA',
            'amount' => 5.00,
            'sort_order' => 1,
        ]);
    }
}
