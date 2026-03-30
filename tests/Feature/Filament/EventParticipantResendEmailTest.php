<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\EventResource\Pages\ViewEvent;
use App\Filament\Resources\EventResource\RelationManagers\MembersRelationManager;
use App\Mail\EventConfirmationMail;
use App\Mail\InvoiceMail;
use App\Models\Event;
use App\Models\Invoice;
use App\Models\InvoiceLine;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class EventParticipantResendEmailTest extends TestCase
{
    use DatabaseTransactions;

    private function makeAdmin(): User
    {
        return User::create([
            'name' => 'Admin',
            'email' => 'resend-admin-' . uniqid() . '@test.ch',
            'password' => bcrypt('password'),
            'role' => 'A',
        ]);
    }

    private function makeMember(array $overrides = []): Member
    {
        return Member::create(array_merge([
            'first_name' => 'Marie',
            'last_name' => 'Test',
            'email' => 'resend-member-' . uniqid() . '@test.ch',
            'statuscode' => 'A',
        ], $overrides));
    }

    public function test_resend_email_for_free_event_sends_confirmation(): void
    {
        Mail::fake();

        $admin = $this->makeAdmin();
        $member = $this->makeMember();
        $event = Event::create([
            'title' => 'Sortie gratuite',
            'starts_at' => '2026-04-15 09:00:00',
            'statuscode' => 'P',
            'price' => 0,
        ]);
        $event->members()->attach($member->id, ['status' => 'N']);

        Livewire::actingAs($admin)
            ->test(MembersRelationManager::class, [
                'ownerRecord' => $event,
                'pageClass' => ViewEvent::class,
            ])
            ->callTableAction('resendEmail', $member);

        Mail::assertSent(EventConfirmationMail::class, function ($mail) use ($member, $event) {
            return $mail->member->id === $member->id
                && $mail->event->id === $event->id;
        });
    }

    public function test_resend_email_for_paid_event_sends_invoice(): void
    {
        Mail::fake();

        $admin = $this->makeAdmin();
        $member = $this->makeMember();
        $event = Event::create([
            'title' => 'Sortie payante',
            'starts_at' => '2026-04-15 09:00:00',
            'statuscode' => 'P',
            'price' => 15,
        ]);
        $event->members()->attach($member->id, ['status' => 'N']);

        $invoice = Invoice::create([
            'member_id' => $member->id,
            'type' => 'E',
            'invoice_number' => '2026-' . $member->id . '-1',
            'amount' => 15,
            'statuscode' => 'E',
        ]);
        InvoiceLine::create([
            'invoice_id' => $invoice->id,
            'description' => 'Sortie payante — 15.04.2026',
            'amount' => 15,
            'sort_order' => 1,
        ]);
        $invoice->events()->attach($event->id);

        Livewire::actingAs($admin)
            ->test(MembersRelationManager::class, [
                'ownerRecord' => $event,
                'pageClass' => ViewEvent::class,
            ])
            ->callTableAction('resendEmail', $member);

        Mail::assertSent(InvoiceMail::class, function ($mail) use ($member) {
            return $mail->invoice->member->id === $member->id;
        });
    }

    public function test_resend_email_shows_success_notification(): void
    {
        Mail::fake();

        $admin = $this->makeAdmin();
        $member = $this->makeMember();
        $event = Event::create([
            'title' => 'Sortie test',
            'starts_at' => '2026-04-15 09:00:00',
            'statuscode' => 'P',
            'price' => 0,
        ]);
        $event->members()->attach($member->id, ['status' => 'N']);

        Livewire::actingAs($admin)
            ->test(MembersRelationManager::class, [
                'ownerRecord' => $event,
                'pageClass' => ViewEvent::class,
            ])
            ->callTableAction('resendEmail', $member)
            ->assertNotified();
    }
}
