<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventMember;
use App\Models\Invoice;
use App\Models\InvoiceLine;
use App\Models\Member;
use App\Models\MemberPhone;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PortalDashboardTest extends TestCase
{
    use DatabaseTransactions;

    private function createAuthenticatedMember(string $statuscode = 'A'): Member
    {
        return Member::create([
            'first_name' => 'Marie',
            'last_name' => 'Dashboard',
            'email' => 'dashboard-test-' . uniqid() . '@example.com',
            'statuscode' => $statuscode,
            'is_invitee' => false,
            'membership_start' => '2025-01-15',
        ]);
    }

    private function authenticatedGet(Member $member, string $uri)
    {
        return $this->withSession([
            'portal_member_id' => $member->id,
            'portal_last_activity' => now()->timestamp,
        ])->get($uri);
    }

    public function test_dashboard_requires_authentication(): void
    {
        $response = $this->get('/portail');

        $response->assertRedirect('/login');
    }

    public function test_dashboard_shows_member_name(): void
    {
        $member = $this->createAuthenticatedMember();

        $response = $this->authenticatedGet($member, '/portail');

        $response->assertOk();
        $response->assertSee('Marie');
        $response->assertSee('Dashboard');
    }

    public function test_dashboard_shows_phone_number(): void
    {
        $member = $this->createAuthenticatedMember();
        MemberPhone::create([
            'member_id' => $member->id,
            'phone_number' => '+41 79 123 45 67',
            'label' => 'Mobile',
            'is_whatsapp' => false,
            'sort_order' => 0,
        ]);

        $response = $this->authenticatedGet($member, '/portail');

        $response->assertSee('+41 79 123 45 67');
    }

    public function test_dashboard_shows_upcoming_events(): void
    {
        $member = $this->createAuthenticatedMember();

        $event = Event::create([
            'title' => 'Sortie vélo test',
            'starts_at' => now()->addDays(7),
            'ends_at' => now()->addDays(7)->addHours(3),
            'location' => 'Genève',
            'statuscode' => 'P',
            'price' => 0,
        ]);

        EventMember::create([
            'event_id' => $event->id,
            'member_id' => $member->id,
            'status' => 'C',
        ]);

        $response = $this->authenticatedGet($member, '/portail');

        $response->assertSee('Sortie vélo test');
        $response->assertSee('Confirmée');
    }

    public function test_dashboard_does_not_show_past_events(): void
    {
        $member = $this->createAuthenticatedMember();

        $event = Event::create([
            'title' => 'Ancien événement',
            'starts_at' => now()->subDays(7),
            'ends_at' => now()->subDays(7)->addHours(3),
            'location' => 'Genève',
            'statuscode' => 'P',
            'price' => 0,
        ]);

        EventMember::create([
            'event_id' => $event->id,
            'member_id' => $member->id,
            'status' => 'C',
        ]);

        $response = $this->authenticatedGet($member, '/portail');

        $response->assertDontSee('Ancien événement');
    }

    public function test_event_detail_page_shows_info(): void
    {
        $member = $this->createAuthenticatedMember();

        $event = Event::create([
            'title' => 'Sortie Salève',
            'description' => 'Belle montée avec vue sur le lac',
            'starts_at' => now()->addDays(7)->setTime(9, 0),
            'ends_at' => now()->addDays(7)->setTime(12, 0),
            'location' => 'Collonges-sous-Salève',
            'statuscode' => 'P',
            'price' => 0,
        ]);

        EventMember::create([
            'event_id' => $event->id,
            'member_id' => $member->id,
            'status' => 'C',
        ]);

        $response = $this->authenticatedGet($member, '/portail/evenement/' . $event->id);

        $response->assertOk();
        $response->assertSee('Sortie Salève');
        $response->assertSee('Belle montée avec vue sur le lac');
        $response->assertSee('Collonges-sous-Salève');
        $response->assertSee('Confirmée');
    }

    public function test_event_detail_shows_register_button_when_not_registered(): void
    {
        $member = $this->createAuthenticatedMember();

        $event = Event::create([
            'title' => 'Sortie ouverte',
            'starts_at' => now()->addDays(7),
            'statuscode' => 'P',
            'price' => 0,
        ]);

        $response = $this->authenticatedGet($member, '/portail/evenement/' . $event->id);

        $response->assertSee('Je m\'inscris', false);
    }

    public function test_event_detail_hides_register_button_when_registered(): void
    {
        $member = $this->createAuthenticatedMember();

        $event = Event::create([
            'title' => 'Sortie inscrite',
            'starts_at' => now()->addDays(7),
            'statuscode' => 'P',
            'price' => 0,
        ]);

        EventMember::create([
            'event_id' => $event->id,
            'member_id' => $member->id,
            'status' => 'C',
        ]);

        $response = $this->authenticatedGet($member, '/portail/evenement/' . $event->id);

        $response->assertDontSee('Je m\'inscris', false);
    }

    public function test_register_free_event_sets_confirmed_and_sends_confirmation(): void
    {
        \Illuminate\Support\Facades\Mail::fake();
        $member = $this->createAuthenticatedMember();

        $event = Event::create([
            'title' => 'Sortie gratuite',
            'starts_at' => now()->addDays(7),
            'statuscode' => 'P',
            'price' => 0,
        ]);

        $response = $this->withSession([
            'portal_member_id' => $member->id,
            'portal_last_activity' => now()->timestamp,
        ])->post('/portail/evenement/' . $event->id . '/inscrire');

        $response->assertRedirect('/portail/evenement/' . $event->id);

        $pivot = EventMember::where('event_id', $event->id)
            ->where('member_id', $member->id)
            ->first();

        $this->assertNotNull($pivot);
        $this->assertEquals('C', $pivot->getRawOriginal('status'));

        \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\EventConfirmationMail::class, function ($mail) use ($member, $event) {
            return $mail->member->id === $member->id && $mail->event->id === $event->id;
        });
    }

    public function test_register_paid_event_sets_inscrite_and_sends_invoice_with_ical(): void
    {
        \Illuminate\Support\Facades\Mail::fake();
        $member = $this->createAuthenticatedMember();

        $event = Event::create([
            'title' => 'Sortie payante',
            'starts_at' => now()->addDays(7),
            'statuscode' => 'P',
            'price' => 25.00,
        ]);

        $this->withSession([
            'portal_member_id' => $member->id,
            'portal_last_activity' => now()->timestamp,
        ])->post('/portail/evenement/' . $event->id . '/inscrire');

        $pivot = EventMember::where('event_id', $event->id)
            ->where('member_id', $member->id)
            ->first();

        $this->assertNotNull($pivot);
        $this->assertEquals('N', $pivot->getRawOriginal('status'));

        $invoice = Invoice::where('member_id', $member->id)->where('type', 'E')->first();
        $this->assertNotNull($invoice);
        $this->assertEquals(25.00, (float) $invoice->amount);

        \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\InvoiceMail::class, function ($mail) {
            return $mail->icalContent !== null && $mail->icalFilename !== null;
        });
    }

    public function test_register_ignores_duplicate(): void
    {
        $member = $this->createAuthenticatedMember();

        $event = Event::create([
            'title' => 'Sortie test',
            'starts_at' => now()->addDays(7),
            'statuscode' => 'P',
            'price' => 0,
        ]);

        EventMember::create([
            'event_id' => $event->id,
            'member_id' => $member->id,
            'status' => 'C',
        ]);

        $response = $this->withSession([
            'portal_member_id' => $member->id,
            'portal_last_activity' => now()->timestamp,
        ])->post('/portail/evenement/' . $event->id . '/inscrire');

        $response->assertRedirect('/portail/evenement/' . $event->id);
        $this->assertEquals(1, EventMember::where('event_id', $event->id)->where('member_id', $member->id)->count());
    }

    public function test_cancel_registration_sets_annulee(): void
    {
        $member = $this->createAuthenticatedMember();

        $event = Event::create([
            'title' => 'Sortie annulable',
            'starts_at' => now()->addDays(7),
            'statuscode' => 'P',
            'price' => 0,
        ]);

        EventMember::create([
            'event_id' => $event->id,
            'member_id' => $member->id,
            'status' => 'C',
        ]);

        $response = $this->withSession([
            'portal_member_id' => $member->id,
            'portal_last_activity' => now()->timestamp,
        ])->post('/portail/evenement/' . $event->id . '/annuler');

        $response->assertRedirect('/portail/evenement/' . $event->id);

        $pivot = EventMember::where('event_id', $event->id)
            ->where('member_id', $member->id)
            ->first();

        $this->assertEquals('X', $pivot->getRawOriginal('status'));
    }

    public function test_cancelled_registration_shows_register_button(): void
    {
        $member = $this->createAuthenticatedMember();

        $event = Event::create([
            'title' => 'Sortie réinscription',
            'starts_at' => now()->addDays(7),
            'statuscode' => 'P',
            'price' => 0,
        ]);

        EventMember::create([
            'event_id' => $event->id,
            'member_id' => $member->id,
            'status' => 'X',
        ]);

        $response = $this->authenticatedGet($member, '/portail/evenement/' . $event->id);

        $response->assertSee('Je m\'inscris', false);
        $response->assertDontSee('Annulée');
    }

    public function test_event_detail_shows_cancel_button_when_registered(): void
    {
        $member = $this->createAuthenticatedMember();

        $event = Event::create([
            'title' => 'Sortie avec annulation',
            'starts_at' => now()->addDays(7),
            'statuscode' => 'P',
            'price' => 0,
        ]);

        EventMember::create([
            'event_id' => $event->id,
            'member_id' => $member->id,
            'status' => 'C',
        ]);

        $response = $this->authenticatedGet($member, '/portail/evenement/' . $event->id);

        $response->assertSee('Je ne peux pas venir', false);
    }

    public function test_dashboard_shows_empty_state(): void
    {
        // Ensure no published future events exist
        \App\Models\Event::where('statuscode', 'P')
            ->where('starts_at', '>=', now())
            ->update(['statuscode' => 'N']);

        $member = $this->createAuthenticatedMember();

        $response = $this->authenticatedGet($member, '/portail');

        $response->assertSee('Aucun événement à venir.');
    }

    public function test_adhesion_page_shows_membership_info(): void
    {
        $member = $this->createAuthenticatedMember();

        $response = $this->authenticatedGet($member, '/portail/adhesion');

        $response->assertOk();
        $response->assertSee('Marie');
        $response->assertSee('Dashboard');
        $response->assertSee('15.01.2025');
        $response->assertSee('Active');
    }

    public function test_adhesion_page_shows_pending_status(): void
    {
        $member = $this->createAuthenticatedMember('P');

        $response = $this->authenticatedGet($member, '/portail/adhesion');

        $response->assertSee('En attente');
    }

    public function test_factures_page_shows_invoices(): void
    {
        $member = $this->createAuthenticatedMember();

        $invoice = Invoice::create([
            'member_id' => $member->id,
            'type' => 'C',
            'cotisation_year' => 2026,
            'invoice_number' => '2026-' . str_pad($member->id, 3, '0', STR_PAD_LEFT) . '-099',
            'amount' => 50.00,
            'statuscode' => 'E',
        ]);

        InvoiceLine::create([
            'invoice_id' => $invoice->id,
            'description' => 'Cotisation annuelle 2026',
            'amount' => 50.00,
            'sort_order' => 0,
        ]);

        $response = $this->authenticatedGet($member, '/portail/factures');

        $response->assertOk();
        $response->assertSee($invoice->invoice_number);
        $response->assertSee('CHF 50.00');
        $response->assertSee('Cotisation annuelle 2026');
        $response->assertSee('Envoyée');
    }

    public function test_factures_page_hides_cancelled_invoices(): void
    {
        $member = $this->createAuthenticatedMember();

        Invoice::create([
            'member_id' => $member->id,
            'type' => 'C',
            'cotisation_year' => 2026,
            'invoice_number' => '2026-' . str_pad($member->id, 3, '0', STR_PAD_LEFT) . '-098',
            'amount' => 50.00,
            'statuscode' => 'X',
        ]);

        $response = $this->authenticatedGet($member, '/portail/factures');

        $response->assertDontSee('Annulée');
    }

    public function test_factures_page_shows_empty_state(): void
    {
        $member = $this->createAuthenticatedMember();

        $response = $this->authenticatedGet($member, '/portail/factures');

        $response->assertSee('Aucune facture.');
    }

    public function test_session_expired_redirects_to_login(): void
    {
        $member = $this->createAuthenticatedMember();

        $response = $this->withSession([
            'portal_member_id' => $member->id,
            'portal_last_activity' => now()->subMinutes(301)->timestamp,
        ])->get('/portail');

        $response->assertRedirect('/login');
    }

    public function test_deleted_member_redirects_to_login(): void
    {
        $member = $this->createAuthenticatedMember();
        $member->delete();

        $response = $this->withSession([
            'portal_member_id' => $member->id,
            'portal_last_activity' => now()->timestamp,
        ])->get('/portail');

        $response->assertRedirect('/login');
    }

    public function test_member_status_changed_to_inactive_redirects(): void
    {
        $member = $this->createAuthenticatedMember();
        $member->update(['statuscode' => 'I']);

        $response = $this->withSession([
            'portal_member_id' => $member->id,
            'portal_last_activity' => now()->timestamp,
        ])->get('/portail');

        $response->assertRedirect('/login');
    }
}
