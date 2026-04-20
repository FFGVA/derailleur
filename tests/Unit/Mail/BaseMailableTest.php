<?php

namespace Tests\Unit\Mail;

use App\Mail\ActivationMail;
use App\Mail\AdhesionConfirmationMail;
use App\Mail\AdhesionWelcomeMail;
use App\Mail\ContactMail;
use App\Mail\EventConfirmationMail;
use App\Mail\EventRegistrationExistingMail;
use App\Mail\EventRegistrationNewMail;
use App\Mail\EventReminderMail;
use App\Mail\ExpiredMemberRegistrationMail;
use App\Mail\InvoiceMail;
use App\Mail\MemberUpdateRequestMail;
use App\Mail\PortalMagicLinkMail;
use App\Models\Event;
use App\Models\Member;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class BaseMailableTest extends TestCase
{
    use DatabaseTransactions;

    private function makeMember(): Member
    {
        return Member::create([
            'first_name' => 'Test',
            'last_name' => 'Base',
            'email' => 'base-mail-' . uniqid() . '@test.ch',
            'statuscode' => 'A',
        ]);
    }

    public function test_all_member_facing_mails_use_config_from_address(): void
    {
        $member = $this->makeMember();
        $event = Event::create(['title' => 'Test', 'starts_at' => now()->addWeek(), 'statuscode' => 'P', 'price' => 0]);

        $expectedFrom = config('association.mail_from_address');

        $mailables = [
            new ActivationMail($member),
            new AdhesionConfirmationMail($member),
            new AdhesionWelcomeMail($member, 'https://example.com/activate'),
            new EventConfirmationMail($member, $event),
            new EventReminderMail($member, $event),
            new EventRegistrationExistingMail($member, $event, 'https://example.com', '1h', 'CHF 0.00'),
            new EventRegistrationNewMail('test@test.ch', $event, 'https://example.com', 'CHF 0.00'),
            new InvoiceMail(
                \App\Models\Invoice::create(['member_id' => $member->id, 'type' => 'C', 'invoice_number' => '2026-0-F01', 'amount' => 50, 'statuscode' => 'N']),
                '%PDF-test', 'test.pdf'
            ),
            new PortalMagicLinkMail($member, 'https://example.com/login', '15 minutes'),
            new MemberUpdateRequestMail($member, [], [], []),
            new ExpiredMemberRegistrationMail($member, $event),
        ];

        foreach ($mailables as $mail) {
            $envelope = $mail->envelope();
            $from = $envelope->from;
            $this->assertEquals(
                $expectedFrom,
                $from->address,
                get_class($mail) . ' does not use config from address'
            );
        }
    }

    public function test_member_facing_mails_with_reply_to_use_config(): void
    {
        $member = $this->makeMember();
        $event = Event::create(['title' => 'Test', 'starts_at' => now()->addWeek(), 'statuscode' => 'P', 'price' => 0]);

        $expectedReplyTo = config('association.mail_reply_to_address');

        // These mail classes should have reply-to set to association contact
        // (MemberUpdateRequestMail excluded — it uses member's email as reply-to)
        $mailables = [
            new ActivationMail($member),
            new AdhesionConfirmationMail($member),
            new EventConfirmationMail($member, $event),
            new EventReminderMail($member, $event),
            new InvoiceMail(
                \App\Models\Invoice::create(['member_id' => $member->id, 'type' => 'C', 'invoice_number' => 'TEST-001', 'amount' => 50, 'statuscode' => 'N']),
                '%PDF-test', 'test.pdf'
            ),
        ];

        foreach ($mailables as $mail) {
            $envelope = $mail->envelope();
            $replyTo = $envelope->replyTo;
            $this->assertNotEmpty($replyTo, get_class($mail) . ' is missing reply-to');
            $this->assertEquals(
                $expectedReplyTo,
                $replyTo[0]->address,
                get_class($mail) . ' does not use config reply-to address'
            );
        }
    }

    public function test_admin_facing_mails_use_config_contact_email(): void
    {
        $member = $this->makeMember();
        $event = Event::create(['title' => 'Test', 'starts_at' => now()->addWeek(), 'statuscode' => 'P', 'price' => 0]);

        $expectedTo = config('association.contact_email');

        $mailables = [
            new ExpiredMemberRegistrationMail($member, $event),
            new MemberUpdateRequestMail($member, [], [], []),
        ];

        foreach ($mailables as $mail) {
            $envelope = $mail->envelope();
            $to = $envelope->to;
            $found = false;
            foreach ($to as $addr) {
                $address = $addr instanceof \Illuminate\Mail\Mailables\Address ? $addr->address : $addr;
                if ($address === $expectedTo) {
                    $found = true;
                    break;
                }
            }
            $this->assertTrue($found, get_class($mail) . ' does not send to config contact email');
        }
    }
}
