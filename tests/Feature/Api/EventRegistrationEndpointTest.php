<?php

namespace Tests\Feature\Api;

use App\Mail\EventRegistrationExistingMail;
use App\Mail\EventRegistrationNewMail;
use App\Models\Event;
use App\Models\Member;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EventRegistrationEndpointTest extends TestCase
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
            'first_name' => 'Test',
            'last_name' => 'Member',
            'email' => 'test-' . uniqid() . '@example.com',
            'statuscode' => 'A',
        ], $overrides));
    }

    public function test_known_member_receives_existing_mail(): void
    {
        Mail::fake();
        $event = $this->makeEvent();
        $member = $this->makeMember();

        $response = $this->postJson('/api/inscription-event', [
            'email' => $member->email,
            'event_id' => $event->id,
        ]);

        $response->assertOk()->assertJson(['ok' => true]);
        Mail::assertSent(EventRegistrationExistingMail::class, fn ($mail) => $mail->member->id === $member->id);
    }

    public function test_unknown_email_receives_new_mail(): void
    {
        Mail::fake();
        $event = $this->makeEvent();

        $response = $this->postJson('/api/inscription-event', [
            'email' => 'unknown@example.com',
            'event_id' => $event->id,
        ]);

        $response->assertOk()->assertJson(['ok' => true]);
        Mail::assertSent(EventRegistrationNewMail::class, fn ($mail) => $mail->email === 'unknown@example.com');
    }

    public function test_honeypot_blocks_mail(): void
    {
        Mail::fake();
        $event = $this->makeEvent();

        $this->postJson('/api/inscription-event', [
            'email' => 'spam@example.com',
            'event_id' => $event->id,
            'website' => 'spam',
        ]);

        Mail::assertNothingSent();
    }

    public function test_unpublished_event_sends_no_mail(): void
    {
        Mail::fake();
        $event = $this->makeEvent(['statuscode' => 'N']);

        $this->postJson('/api/inscription-event', [
            'email' => 'test@example.com',
            'event_id' => $event->id,
        ]);

        Mail::assertNothingSent();
    }

    public function test_full_event_sends_no_mail(): void
    {
        Mail::fake();
        $event = $this->makeEvent(['max_participants' => 1]);
        $member = $this->makeMember();
        $event->members()->attach($member->id, ['status' => 'C']);

        $this->postJson('/api/inscription-event', [
            'email' => 'new@example.com',
            'event_id' => $event->id,
        ]);

        Mail::assertNothingSent();
    }

    public function test_missing_email_returns_422(): void
    {
        $event = $this->makeEvent();

        $response = $this->postJson('/api/inscription-event', [
            'event_id' => $event->id,
        ]);

        $response->assertStatus(422);
    }

    public function test_inactive_member_receives_new_mail(): void
    {
        Mail::fake();
        $event = $this->makeEvent();
        $member = $this->makeMember(['statuscode' => 'I']);

        $this->postJson('/api/inscription-event', [
            'email' => $member->email,
            'event_id' => $event->id,
        ]);

        Mail::assertSent(EventRegistrationNewMail::class);
        Mail::assertNotSent(EventRegistrationExistingMail::class);
    }

    public function test_nonexistent_event_returns_ok(): void
    {
        Mail::fake();

        $response = $this->postJson('/api/inscription-event', [
            'email' => 'test@example.com',
            'event_id' => 99999,
        ]);

        $response->assertOk()->assertJson(['ok' => true]);
        Mail::assertNothingSent();
    }
}
