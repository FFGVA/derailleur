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

    public function test_dashboard_shows_empty_state(): void
    {
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
        $response->assertSee('Actif');
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
