<?php

namespace Tests\Feature;

use App\Mail\EventConfirmationMail;
use App\Mail\InvoiceMail;
use App\Models\Event;
use App\Models\EventMember;
use App\Models\Member;
use App\Models\MemberMagicToken;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EventRegistrationFlowTest extends TestCase
{
    use DatabaseTransactions;

    private function makeEvent(array $overrides = []): Event
    {
        return Event::create(array_merge([
            'title' => 'Sortie Test',
            'starts_at' => now()->addWeek(),
            'statuscode' => 'P',
            'price' => '10.00',
            'price_non_member' => '25.00',
        ], $overrides));
    }

    private function makeMember(array $overrides = []): Member
    {
        return Member::create(array_merge([
            'first_name' => 'Marie',
            'last_name' => 'Test',
            'email' => 'marie-' . uniqid() . '@example.com',
            'statuscode' => 'A',
        ], $overrides));
    }

    // --- Confirmation flow (known member) ---

    public function test_confirmer_registers_member_and_logs_in(): void
    {
        Mail::fake();
        $event = $this->makeEvent(['price' => 0]);
        $member = $this->makeMember();
        [$token, $rawToken] = MemberMagicToken::generateFor($member, 60);

        $response = $this->get("/inscription-event/confirmer?token={$rawToken}&event_id={$event->id}");

        $response->assertRedirect(route('portail.evenement', $event));
        $this->assertDatabaseHas('event_member', [
            'event_id' => $event->id,
            'member_id' => $member->id,
        ]);
        $response->assertSessionHas('portal_member_id', $member->id);
    }

    public function test_confirmer_sends_invoice_for_paid_event(): void
    {
        Mail::fake();
        $event = $this->makeEvent(['price' => '10.00']);
        $member = $this->makeMember();
        [$token, $rawToken] = MemberMagicToken::generateFor($member, 60);

        $this->get("/inscription-event/confirmer?token={$rawToken}&event_id={$event->id}");

        Mail::assertSent(InvoiceMail::class);
    }

    public function test_confirmer_sends_confirmation_for_free_event(): void
    {
        Mail::fake();
        $event = $this->makeEvent(['price' => 0]);
        $member = $this->makeMember();
        [$token, $rawToken] = MemberMagicToken::generateFor($member, 60);

        $this->get("/inscription-event/confirmer?token={$rawToken}&event_id={$event->id}");

        Mail::assertSent(EventConfirmationMail::class);
    }

    public function test_confirmer_invalid_token_redirects(): void
    {
        $event = $this->makeEvent();

        $response = $this->get("/inscription-event/confirmer?token=invalid&event_id={$event->id}");

        $response->assertRedirect(route('portail.login'));
    }

    public function test_confirmer_does_not_duplicate_registration(): void
    {
        Mail::fake();
        $event = $this->makeEvent(['price' => 0]);
        $member = $this->makeMember();
        EventMember::create(['event_id' => $event->id, 'member_id' => $member->id, 'status' => 'C']);

        [$token, $rawToken] = MemberMagicToken::generateFor($member, 60);
        $this->get("/inscription-event/confirmer?token={$rawToken}&event_id={$event->id}");

        $this->assertSame(1, EventMember::where('event_id', $event->id)->where('member_id', $member->id)->count());
    }

    public function test_confirmer_uses_member_price_for_active(): void
    {
        Mail::fake();
        $event = $this->makeEvent(['price' => '10.00', 'price_non_member' => '25.00']);
        $member = $this->makeMember(['statuscode' => 'A']);
        [$token, $rawToken] = MemberMagicToken::generateFor($member, 60);

        $this->get("/inscription-event/confirmer?token={$rawToken}&event_id={$event->id}");

        Mail::assertSent(InvoiceMail::class, function ($mail) {
            return $mail->invoice->amount == '10.00';
        });
    }

    public function test_confirmer_uses_non_member_price_for_non_member(): void
    {
        Mail::fake();
        $event = $this->makeEvent(['price' => '10.00', 'price_non_member' => '25.00']);
        $member = $this->makeMember(['statuscode' => 'N']);
        [$token, $rawToken] = MemberMagicToken::generateFor($member, 60);

        $this->get("/inscription-event/confirmer?token={$rawToken}&event_id={$event->id}");

        Mail::assertSent(InvoiceMail::class, function ($mail) {
            return $mail->invoice->amount == '25.00';
        });
    }

    // --- New member flow ---

    public function test_nouveau_form_shows_with_valid_signature(): void
    {
        $event = $this->makeEvent();
        $url = URL::temporarySignedRoute('inscription-event.nouveau', now()->addHours(24), [
            'event_id' => $event->id,
            'email' => 'new@example.com',
        ]);

        $response = $this->get($url);

        $response->assertOk();
        $response->assertSee('new@example.com');
        $response->assertSee($event->title);
    }

    public function test_nouveau_form_rejects_invalid_signature(): void
    {
        $event = $this->makeEvent();

        $response = $this->get("/inscription-event/nouveau?event_id={$event->id}&email=new@example.com&signature=invalid");

        $response->assertRedirect(route('portail.login'));
    }

    public function test_nouveau_store_creates_member_and_registers(): void
    {
        Mail::fake();
        $event = $this->makeEvent(['price' => 0]);
        $url = URL::temporarySignedRoute('inscription-event.nouveau', now()->addHours(24), [
            'event_id' => $event->id,
            'email' => 'new@example.com',
        ]);

        $response = $this->post($url, [
            'prenom' => 'Sophie',
            'nom' => 'Nouvelle',
            'email' => 'new@example.com',
            'telephone' => '079 123 45 67',
            'event_id' => $event->id,
        ]);

        $response->assertRedirect(route('portail.evenement', $event));

        $member = Member::where('email', 'new@example.com')->first();
        $this->assertNotNull($member);
        $this->assertSame('N', $member->getRawOriginal('statuscode'));
        $this->assertSame('Sophie', $member->first_name);
        $this->assertDatabaseHas('event_member', [
            'event_id' => $event->id,
            'member_id' => $member->id,
        ]);
        $this->assertDatabaseHas('member_phones', [
            'member_id' => $member->id,
            'phone_number' => '+41 79 123 45 67',
        ]);
    }

    public function test_nouveau_store_creates_invoice_for_paid_event(): void
    {
        Mail::fake();
        $event = $this->makeEvent(['price' => '10.00', 'price_non_member' => '25.00']);
        $url = URL::temporarySignedRoute('inscription-event.nouveau', now()->addHours(24), [
            'event_id' => $event->id,
            'email' => 'paid@example.com',
        ]);

        $this->post($url, [
            'prenom' => 'Léa',
            'nom' => 'Payante',
            'email' => 'paid@example.com',
            'telephone' => '079 000 00 00',
            'event_id' => $event->id,
        ]);

        Mail::assertSent(InvoiceMail::class, function ($mail) {
            return $mail->invoice->amount == '25.00'; // Non-member price
        });
    }

    public function test_nouveau_store_logs_into_portal(): void
    {
        Mail::fake();
        $event = $this->makeEvent(['price' => 0]);
        $url = URL::temporarySignedRoute('inscription-event.nouveau', now()->addHours(24), [
            'event_id' => $event->id,
            'email' => 'session@example.com',
        ]);

        $response = $this->post($url, [
            'prenom' => 'Test',
            'nom' => 'Session',
            'email' => 'session@example.com',
            'telephone' => '079 111 11 11',
            'event_id' => $event->id,
        ]);

        $member = Member::where('email', 'session@example.com')->first();
        $response->assertSessionHas('portal_member_id', $member->id);
    }

    public function test_nouveau_store_validates_required_fields(): void
    {
        $event = $this->makeEvent();
        $url = URL::temporarySignedRoute('inscription-event.nouveau', now()->addHours(24), [
            'event_id' => $event->id,
            'email' => 'test@example.com',
        ]);

        $response = $this->post($url, [
            'email' => 'test@example.com',
            'event_id' => $event->id,
        ]);

        $response->assertSessionHasErrors(['prenom', 'nom', 'telephone']);
    }

    public function test_nouveau_store_whatsapp_flag(): void
    {
        Mail::fake();
        $event = $this->makeEvent(['price' => 0]);
        $url = URL::temporarySignedRoute('inscription-event.nouveau', now()->addHours(24), [
            'event_id' => $event->id,
            'email' => 'wa@example.com',
        ]);

        $this->post($url, [
            'prenom' => 'Test',
            'nom' => 'WhatsApp',
            'email' => 'wa@example.com',
            'telephone' => '079 222 22 22',
            'whatsapp' => '1',
            'event_id' => $event->id,
        ]);

        $member = Member::where('email', 'wa@example.com')->first();
        $this->assertDatabaseHas('member_phones', [
            'member_id' => $member->id,
            'is_whatsapp' => 1,
        ]);
    }
}
