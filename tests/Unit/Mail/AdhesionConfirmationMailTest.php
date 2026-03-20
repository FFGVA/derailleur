<?php

namespace Tests\Unit\Mail;

use App\Mail\AdhesionConfirmationMail;
use App\Models\Member;
use Tests\TestCase;

class AdhesionConfirmationMailTest extends TestCase
{
    private Member $member;
    private string $pdfContent;
    private AdhesionConfirmationMail $mail;

    protected function setUp(): void
    {
        parent::setUp();

        $this->member = new Member([
            'first_name' => 'Marie',
            'last_name' => 'Dupont',
            'email' => 'marie.dupont@example.com',
        ]);
        $this->member->id = 999;

        $this->pdfContent = '%PDF-1.4 fake pdf content for testing';

        $this->mail = new AdhesionConfirmationMail($this->member, $this->pdfContent, 'ffgva_Dupont_Marie-facture-2026-999-001.pdf');
    }

    public function test_subject_is_correct(): void
    {
        $this->mail->assertHasSubject('Confirmation de ton inscription - FFGVA');
    }

    public function test_sent_to_member_email(): void
    {
        $this->mail->assertTo($this->member->email);
    }

    public function test_content_contains_confirmation_text(): void
    {
        $this->mail->assertSeeInHtml('Merci');
    }

    public function test_content_mentions_payment(): void
    {
        $this->mail->assertSeeInHtml('paiement');
    }

    public function test_has_pdf_attachment(): void
    {
        $this->mail->assertHasAttachedData($this->pdfContent, 'ffgva_Dupont_Marie-facture-2026-999-001.pdf', [
            'mime' => 'application/pdf',
        ]);
    }
}
