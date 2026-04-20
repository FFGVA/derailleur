<?php

namespace Tests\Unit\Services;

use App\Enums\EventMemberStatus;
use App\Mail\EventConfirmationMail;
use App\Mail\InvoiceMail;
use App\Models\Event;
use App\Models\EventMember;
use App\Models\Member;
use App\Services\EventRegistrationService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EventRegistrationServiceTest extends TestCase
{
    use DatabaseTransactions;

    private function makeMember(array $overrides = []): Member
    {
        return Member::create(array_merge([
            'first_name' => 'Marie',
            'last_name' => 'Dupont',
            'email' => 'evtreg-' . uniqid() . '@test.ch',
            'statuscode' => 'A',
            'address' => 'Rue Test 1',
            'postal_code' => '1200',
            'city' => 'Genève',
        ], $overrides));
    }

    public function test_register_creates_pivot_for_free_event(): void
    {
        Mail::fake();

        $member = $this->makeMember();
        $event = Event::create(['title' => 'Sortie gratuite', 'starts_at' => now()->addWeek(), 'statuscode' => 'P', 'price' => 0]);

        EventRegistrationService::register($member, $event);

        $pivot = EventMember::where('event_id', $event->id)->where('member_id', $member->id)->first();
        $this->assertNotNull($pivot);
        $this->assertEquals(EventMemberStatus::Confirme->value, $pivot->getRawOriginal('status'));
    }

    public function test_register_sends_confirmation_for_free_event(): void
    {
        Mail::fake();

        $member = $this->makeMember();
        $event = Event::create(['title' => 'Sortie gratuite', 'starts_at' => now()->addWeek(), 'statuscode' => 'P', 'price' => 0]);

        EventRegistrationService::register($member, $event);

        Mail::assertSent(EventConfirmationMail::class);
        Mail::assertNotSent(InvoiceMail::class);
    }

    public function test_register_creates_pivot_for_paid_event(): void
    {
        Mail::fake();

        $member = $this->makeMember();
        $event = Event::create(['title' => 'Sortie payante', 'starts_at' => now()->addWeek(), 'statuscode' => 'P', 'price' => 25]);

        EventRegistrationService::register($member, $event);

        $pivot = EventMember::where('event_id', $event->id)->where('member_id', $member->id)->first();
        $this->assertNotNull($pivot);
        $this->assertEquals(EventMemberStatus::Inscrit->value, $pivot->getRawOriginal('status'));
    }

    public function test_register_sends_invoice_for_paid_event(): void
    {
        Mail::fake();

        $member = $this->makeMember();
        $event = Event::create(['title' => 'Sortie payante', 'starts_at' => now()->addWeek(), 'statuscode' => 'P', 'price' => 25]);

        EventRegistrationService::register($member, $event);

        Mail::assertSent(InvoiceMail::class);
        Mail::assertNotSent(EventConfirmationMail::class);
    }

    public function test_register_skips_if_already_registered(): void
    {
        Mail::fake();

        $member = $this->makeMember();
        $event = Event::create(['title' => 'Sortie', 'starts_at' => now()->addWeek(), 'statuscode' => 'P', 'price' => 0]);

        EventRegistrationService::register($member, $event);
        Mail::fake(); // reset
        EventRegistrationService::register($member, $event);

        Mail::assertNothingSent();
    }

    public function test_register_re_registers_cancelled_member(): void
    {
        Mail::fake();

        $member = $this->makeMember();
        $event = Event::create(['title' => 'Sortie', 'starts_at' => now()->addWeek(), 'statuscode' => 'P', 'price' => 0]);

        EventMember::create(['event_id' => $event->id, 'member_id' => $member->id, 'status' => EventMemberStatus::Annule->value]);

        EventRegistrationService::register($member, $event);

        $pivot = EventMember::where('event_id', $event->id)->where('member_id', $member->id)->first();
        $this->assertEquals(EventMemberStatus::Confirme->value, $pivot->getRawOriginal('status'));
        Mail::assertSent(EventConfirmationMail::class);
    }
}
