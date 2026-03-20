<?php

namespace Tests\Unit\Mail;

use App\Mail\AdhesionWelcomeMail;
use App\Models\Member;
use Tests\TestCase;

class AdhesionWelcomeMailTest extends TestCase
{
    private Member $member;
    private string $activationUrl;
    private AdhesionWelcomeMail $mail;

    protected function setUp(): void
    {
        parent::setUp();

        $this->member = new Member([
            'first_name' => 'Marie',
            'last_name' => 'Dupont',
            'email' => 'marie.dupont@example.com',
        ]);
        $this->member->id = 999;

        $this->activationUrl = 'https://derailleur.ffgva.ch/adhesion/confirmer?token=abc123&email=marie.dupont@example.com';

        $this->mail = new AdhesionWelcomeMail($this->member, $this->activationUrl);
    }

    public function test_subject_is_correct(): void
    {
        $this->mail->assertHasSubject('Bienvenue chez Fast and Female Geneva !');
    }

    public function test_sent_to_member_email(): void
    {
        $this->mail->assertTo($this->member->email);
    }

    public function test_content_contains_prenom(): void
    {
        $this->mail->assertSeeInHtml($this->member->first_name);
    }

    public function test_content_contains_activation_link(): void
    {
        $this->mail->assertSeeInHtml($this->activationUrl);
    }

    public function test_content_contains_lgpd_notice(): void
    {
        $this->mail->assertSeeInHtml('protection des données');
    }
}
