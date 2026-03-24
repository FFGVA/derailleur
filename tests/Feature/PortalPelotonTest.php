<?php

namespace Tests\Feature;

use App\Mail\InvoiceMail;
use App\Models\Event;
use App\Models\EventChef;
use App\Models\EventMember;
use App\Models\Invoice;
use App\Models\Member;
use App\Models\MemberPhone;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PortalPelotonTest extends TestCase
{
    use DatabaseTransactions;

    private function createChef(): Member
    {
        return Member::create([
            'first_name' => 'Sophie',
            'last_name' => 'Cheffe',
            'email' => 'chef-test-' . uniqid() . '@example.com',
            'statuscode' => 'A',
            'is_invitee' => false,
        ]);
    }

    private function createMember(string $firstName = 'Alice', string $lastName = 'Membre'): Member
    {
        return Member::create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => strtolower($firstName) . '-' . uniqid() . '@example.com',
            'statuscode' => 'A',
            'is_invitee' => false,
        ]);
    }

    private function authenticatedGet(Member $member, string $uri)
    {
        return $this->withSession([
            'portal_member_id' => $member->id,
            'portal_last_activity' => now()->timestamp,
        ])->get($uri);
    }

    private function authenticatedPost(Member $member, string $uri, array $data = [])
    {
        return $this->withSession([
            'portal_member_id' => $member->id,
            'portal_last_activity' => now()->timestamp,
        ])->post($uri, $data);
    }

    private function createEventWithChef(Member $chef, array $overrides = []): Event
    {
        $event = Event::create(array_merge([
            'title' => 'Sortie test',
            'starts_at' => now()->addDays(3),
            'statuscode' => 'P',
            'price' => 0,
        ], $overrides));

        EventChef::create([
            'event_id' => $event->id,
            'member_id' => $chef->id,
            'sort_order' => 0,
        ]);

        return $event;
    }

    // ── Dashboard button ──

    public function test_dashboard_shows_peloton_button_for_chef(): void
    {
        $chef = $this->createChef();
        $this->createEventWithChef($chef);

        $response = $this->authenticatedGet($chef, '/portail');

        $response->assertSee('Peloton');
        $response->assertSee(route('portail.peloton'));
    }

    public function test_dashboard_hides_peloton_button_for_non_chef(): void
    {
        $member = $this->createMember();

        $response = $this->authenticatedGet($member, '/portail');

        $response->assertDontSee('Peloton');
    }

    // ── Peloton list page ──

    public function test_peloton_page_requires_authentication(): void
    {
        $response = $this->get('/portail/peloton');
        $response->assertRedirect('/login');
    }

    public function test_peloton_page_shows_upcoming_led_events(): void
    {
        $chef = $this->createChef();
        $this->createEventWithChef($chef, ['title' => 'Sortie Jura']);

        $response = $this->authenticatedGet($chef, '/portail/peloton');

        $response->assertOk();
        $response->assertSee('Sortie Jura');
    }

    public function test_peloton_page_shows_events_up_to_1_week_past(): void
    {
        $chef = $this->createChef();
        $this->createEventWithChef($chef, ['title' => 'Sortie récente', 'starts_at' => now()->subDays(5)]);

        $response = $this->authenticatedGet($chef, '/portail/peloton');

        $response->assertSee('Sortie récente');
    }

    public function test_peloton_page_hides_events_older_than_1_week(): void
    {
        $chef = $this->createChef();
        $this->createEventWithChef($chef, ['title' => 'Sortie ancienne', 'starts_at' => now()->subDays(10)]);

        $response = $this->authenticatedGet($chef, '/portail/peloton');

        $response->assertDontSee('Sortie ancienne');
    }

    public function test_peloton_page_hides_events_led_by_others(): void
    {
        $chef = $this->createChef();
        $otherChef = $this->createMember('Autre', 'Chef');
        $this->createEventWithChef($otherChef, ['title' => 'Sortie autre chef']);

        $response = $this->authenticatedGet($chef, '/portail/peloton');

        $response->assertDontSee('Sortie autre chef');
    }

    // ── Event detail page ──

    public function test_peloton_event_shows_detail(): void
    {
        $chef = $this->createChef();
        $event = $this->createEventWithChef($chef, [
            'title' => 'Col de la Faucille',
            'description' => 'Montée mythique du Jura',
            'starts_at' => now()->addDays(3)->setTime(9, 0),
            'ends_at' => now()->addDays(3)->setTime(12, 0),
            'location' => 'Gex, France',
        ]);

        $response = $this->authenticatedGet($chef, '/portail/peloton/' . $event->id);

        $response->assertOk();
        $response->assertSee('Col de la Faucille');
        $response->assertSee('Gex, France');
    }

    public function test_peloton_event_shows_participants(): void
    {
        $chef = $this->createChef();
        $rider = $this->createMember('Léa', 'Cycliste');
        MemberPhone::create([
            'member_id' => $rider->id,
            'phone_number' => '+41 79 111 22 33',
            'label' => 'Mobile',
            'is_whatsapp' => true,
            'sort_order' => 0,
        ]);

        $event = $this->createEventWithChef($chef);

        EventMember::create([
            'event_id' => $event->id,
            'member_id' => $rider->id,
            'status' => 'C',
        ]);

        $response = $this->authenticatedGet($chef, '/portail/peloton/' . $event->id);

        $response->assertSee('Léa');
        $response->assertSee('Cycliste');
        $response->assertSee('+41 79 111 22 33');
    }

    public function test_peloton_event_shows_no_photo_icon(): void
    {
        $chef = $this->createChef();
        $rider = Member::create([
            'first_name' => 'Nora',
            'last_name' => 'Timide',
            'email' => 'nora-' . uniqid() . '@example.com',
            'statuscode' => 'A',
            'is_invitee' => false,
            'photo_ok' => false,
        ]);
        $event = $this->createEventWithChef($chef);
        EventMember::create([
            'event_id' => $event->id,
            'member_id' => $rider->id,
            'status' => 'C',
        ]);

        $response = $this->authenticatedGet($chef, '/portail/peloton/' . $event->id);

        $response->assertSee('Pas de photo');
    }

    public function test_peloton_event_hides_no_photo_icon_when_ok(): void
    {
        $chef = $this->createChef();
        $rider = $this->createMember('Léa', 'Souriante');
        $event = $this->createEventWithChef($chef);
        EventMember::create([
            'event_id' => $event->id,
            'member_id' => $rider->id,
            'status' => 'C',
        ]);

        $response = $this->authenticatedGet($chef, '/portail/peloton/' . $event->id);

        $response->assertDontSee('Pas de photo');
    }

    public function test_peloton_event_forbidden_for_non_chef(): void
    {
        $chef = $this->createChef();
        $other = $this->createMember();
        $event = $this->createEventWithChef($chef);

        $response = $this->authenticatedGet($other, '/portail/peloton/' . $event->id);

        $response->assertStatus(403);
    }

    // ── Member detail page ──

    public function test_peloton_member_shows_participant(): void
    {
        $chef = $this->createChef();
        $rider = $this->createMember('Léa', 'Cycliste');
        $event = $this->createEventWithChef($chef);
        EventMember::create([
            'event_id' => $event->id,
            'member_id' => $rider->id,
            'status' => 'C',
        ]);

        $response = $this->authenticatedGet($chef, "/portail/peloton/{$event->id}/membre/{$rider->id}");

        $response->assertOk();
        $response->assertSee('Léa');
        $response->assertSee('Cycliste');
    }

    public function test_peloton_member_allows_chef_to_view_herself(): void
    {
        $chef = $this->createChef();
        $event = $this->createEventWithChef($chef);

        $response = $this->authenticatedGet($chef, "/portail/peloton/{$event->id}/membre/{$chef->id}");

        $response->assertOk();
        $response->assertSee('Sophie');
    }

    public function test_peloton_member_forbidden_for_non_participant(): void
    {
        $chef = $this->createChef();
        $outsider = $this->createMember('Externe', 'Personne');
        $event = $this->createEventWithChef($chef);

        $response = $this->authenticatedGet($chef, "/portail/peloton/{$event->id}/membre/{$outsider->id}");

        $response->assertStatus(403);
    }

    // ── Toggle presence ──

    public function test_toggle_presence_null_to_true(): void
    {
        $chef = $this->createChef();
        $rider = $this->createMember();
        $event = $this->createEventWithChef($chef);
        $pivot = EventMember::create([
            'event_id' => $event->id,
            'member_id' => $rider->id,
            'status' => 'C',
            'present' => null,
        ]);

        $this->authenticatedPost($chef, "/portail/peloton/{$event->id}/presence/{$rider->id}");

        $pivot->refresh();
        $this->assertTrue($pivot->present);
    }

    public function test_toggle_presence_true_to_false(): void
    {
        $chef = $this->createChef();
        $rider = $this->createMember();
        $event = $this->createEventWithChef($chef);
        $pivot = EventMember::create([
            'event_id' => $event->id,
            'member_id' => $rider->id,
            'status' => 'C',
            'present' => true,
        ]);

        $this->authenticatedPost($chef, "/portail/peloton/{$event->id}/presence/{$rider->id}");

        $pivot->refresh();
        $this->assertFalse($pivot->present);
    }

    public function test_toggle_presence_false_to_null(): void
    {
        $chef = $this->createChef();
        $rider = $this->createMember();
        $event = $this->createEventWithChef($chef);
        $pivot = EventMember::create([
            'event_id' => $event->id,
            'member_id' => $rider->id,
            'status' => 'C',
            'present' => false,
        ]);

        $this->authenticatedPost($chef, "/portail/peloton/{$event->id}/presence/{$rider->id}");

        $pivot->refresh();
        $this->assertNull($pivot->present);
    }

    public function test_toggle_presence_forbidden_for_non_chef(): void
    {
        $chef = $this->createChef();
        $other = $this->createMember('Autre', 'Personne');
        $rider = $this->createMember('Léa', 'Cycliste');
        $event = $this->createEventWithChef($chef);
        EventMember::create([
            'event_id' => $event->id,
            'member_id' => $rider->id,
            'status' => 'C',
        ]);

        $response = $this->authenticatedPost($other, "/portail/peloton/{$event->id}/presence/{$rider->id}");

        $response->assertStatus(403);
    }

    public function test_toggle_presence_redirects_back(): void
    {
        $chef = $this->createChef();
        $rider = $this->createMember();
        $event = $this->createEventWithChef($chef);
        EventMember::create([
            'event_id' => $event->id,
            'member_id' => $rider->id,
            'status' => 'C',
        ]);

        $response = $this->authenticatedPost($chef, "/portail/peloton/{$event->id}/presence/{$rider->id}");

        $response->assertRedirect("/portail/peloton/{$event->id}");
    }

    // ── Add participant ──

    public function test_add_participant_creates_event_member(): void
    {
        $chef = $this->createChef();
        $rider = $this->createMember('Léa', 'Nouvelle');
        $event = $this->createEventWithChef($chef);

        $this->authenticatedPost($chef, "/portail/peloton/{$event->id}/ajouter", [
            'member_id' => $rider->id,
        ]);

        $pivot = EventMember::where('event_id', $event->id)
            ->where('member_id', $rider->id)
            ->first();

        $this->assertNotNull($pivot);
        $this->assertEquals('C', $pivot->getRawOriginal('status'));
        $this->assertTrue($pivot->present);
    }

    public function test_add_participant_redirects_back(): void
    {
        $chef = $this->createChef();
        $rider = $this->createMember('Léa', 'Nouvelle');
        $event = $this->createEventWithChef($chef);

        $response = $this->authenticatedPost($chef, "/portail/peloton/{$event->id}/ajouter", [
            'member_id' => $rider->id,
        ]);

        $response->assertRedirect("/portail/peloton/{$event->id}");
    }

    public function test_add_participant_forbidden_for_non_chef(): void
    {
        $chef = $this->createChef();
        $other = $this->createMember('Autre', 'Personne');
        $rider = $this->createMember('Léa', 'Nouvelle');
        $event = $this->createEventWithChef($chef);

        $response = $this->authenticatedPost($other, "/portail/peloton/{$event->id}/ajouter", [
            'member_id' => $rider->id,
        ]);

        $response->assertStatus(403);
    }

    public function test_add_participant_ignores_duplicate(): void
    {
        $chef = $this->createChef();
        $rider = $this->createMember('Léa', 'Déjà');
        $event = $this->createEventWithChef($chef);
        EventMember::create([
            'event_id' => $event->id,
            'member_id' => $rider->id,
            'status' => 'N',
        ]);

        $response = $this->authenticatedPost($chef, "/portail/peloton/{$event->id}/ajouter", [
            'member_id' => $rider->id,
        ]);

        $response->assertRedirect("/portail/peloton/{$event->id}");
        $this->assertEquals(1, EventMember::where('event_id', $event->id)->where('member_id', $rider->id)->count());
    }

    public function test_add_participant_dropdown_excludes_existing(): void
    {
        $chef = $this->createChef();
        $existing = $this->createMember('Déjà', 'Là');
        $available = $this->createMember('Nouvelle', 'Membre');
        $event = $this->createEventWithChef($chef);
        EventMember::create([
            'event_id' => $event->id,
            'member_id' => $existing->id,
            'status' => 'C',
        ]);

        $response = $this->authenticatedGet($chef, '/portail/peloton/' . $event->id);

        $response->assertDontSee('value="' . $existing->id . '"', false);
        $response->assertSee('value="' . $available->id . '"', false);
    }

    public function test_add_participant_sends_invoice_when_price_positive(): void
    {
        Mail::fake();
        $chef = $this->createChef();
        $rider = $this->createMember('Léa', 'Payante');
        $event = $this->createEventWithChef($chef, ['title' => 'Sortie payante', 'price' => 25.00]);

        $this->authenticatedPost($chef, "/portail/peloton/{$event->id}/ajouter", [
            'member_id' => $rider->id,
        ]);

        $invoice = Invoice::where('member_id', $rider->id)->where('type', 'E')->first();
        $this->assertNotNull($invoice);
        $this->assertEquals(25.00, (float) $invoice->amount);
        $this->assertEquals('E', $invoice->getRawOriginal('statuscode'));

        Mail::assertSent(InvoiceMail::class, function ($mail) use ($rider) {
            return $mail->invoice->member_id === $rider->id
                && $mail->icalContent !== null
                && $mail->icalFilename !== null;
        });
    }

    public function test_add_participant_no_invoice_when_price_zero(): void
    {
        Mail::fake();
        $chef = $this->createChef();
        $rider = $this->createMember('Léa', 'Gratuite');
        $event = $this->createEventWithChef($chef, ['title' => 'Sortie gratuite']);

        $this->authenticatedPost($chef, "/portail/peloton/{$event->id}/ajouter", [
            'member_id' => $rider->id,
        ]);

        $this->assertNull(Invoice::where('member_id', $rider->id)->first());
        Mail::assertNothingSent();
    }
}
