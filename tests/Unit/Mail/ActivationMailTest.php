<?php

namespace Tests\Unit\Mail;

use App\Mail\ActivationMail;
use App\Models\Member;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ActivationMailTest extends TestCase
{
    use DatabaseTransactions;

    private function makeMember(): Member
    {
        return Member::create([
            'first_name' => 'Julie',
            'last_name' => 'Testeur',
            'email' => 'activation-test-' . uniqid() . '@test.ch',
            'statuscode' => 'A',
        ]);
    }

    public function test_subject_is_correct(): void
    {
        $member = $this->makeMember();
        $mail = new ActivationMail($member);

        $this->assertEquals('Bienvenue chez Fast and Female Geneva !', $mail->envelope()->subject);
    }

    public function test_sent_to_member_email(): void
    {
        $member = $this->makeMember();
        $mail = new ActivationMail($member);

        $to = $mail->envelope()->to;
        $this->assertEquals($member->email, $to[0] instanceof \Illuminate\Mail\Mailables\Address ? $to[0]->address : $to[0]);
    }

    public function test_content_contains_prenom(): void
    {
        $member = $this->makeMember();
        $mail = new ActivationMail($member);

        $mail->assertSeeInHtml($member->first_name);
    }

    public function test_content_contains_login_link(): void
    {
        $member = $this->makeMember();
        $mail = new ActivationMail($member);

        $mail->assertSeeInHtml('derailleur.ffgva.ch/login');
    }

    public function test_has_voucher_attachment(): void
    {
        $member = $this->makeMember();
        $mail = new ActivationMail($member);

        $attachments = $mail->attachments();
        $this->assertCount(1, $attachments);
    }
}
