<?php

namespace Tests\Unit\Services;

use App\Enums\MemberStatus;
use App\Mail\AdhesionConfirmationMail;
use App\Mail\AdhesionWelcomeMail;
use App\Mail\InvoiceMail;
use App\Models\Member;
use App\Models\MemberPhone;
use App\Services\AdhesionService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdhesionServiceTest extends TestCase
{
    use DatabaseTransactions;

    private function makeMember(array $overrides = []): Member
    {
        return Member::create(array_merge([
            'first_name' => 'Marie',
            'last_name' => 'Dupont',
            'email' => 'adhesion-svc-' . uniqid() . '@test.ch',
            'statuscode' => 'N',
            'address' => 'Rue Test 1',
            'postal_code' => '1200',
            'city' => 'Genève',
        ], $overrides));
    }

    // ── submitNew ──

    public function test_submit_new_creates_member_with_status_p(): void
    {
        Mail::fake();

        $result = AdhesionService::submitNew([
            'prenom' => 'Julie',
            'nom' => 'Test',
            'email' => 'new-adhesion-' . uniqid() . '@test.ch',
            'telephone' => '+41 79 123 45 67',
            'photo_ok' => true,
            'metadata' => ['type_velo' => 'route'],
        ]);

        $this->assertEquals(MemberStatus::EnAttente->value, $result->getRawOriginal('statuscode'));
        $this->assertNotNull($result->activation_token);
    }

    public function test_submit_new_sends_welcome_email(): void
    {
        Mail::fake();

        AdhesionService::submitNew([
            'prenom' => 'Julie',
            'nom' => 'Test',
            'email' => 'welcome-' . uniqid() . '@test.ch',
            'telephone' => '+41 79 123 45 67',
            'photo_ok' => true,
        ]);

        Mail::assertSent(AdhesionWelcomeMail::class);
    }

    public function test_submit_new_resubmission_updates_pending_member(): void
    {
        Mail::fake();

        $email = 'resub-' . uniqid() . '@test.ch';
        AdhesionService::submitNew([
            'prenom' => 'Ancien',
            'nom' => 'Nom',
            'email' => $email,
            'telephone' => '+41 79 111 11 11',
            'photo_ok' => true,
        ]);

        AdhesionService::submitNew([
            'prenom' => 'Nouveau',
            'nom' => 'Prénom',
            'email' => $email,
            'telephone' => '+41 79 222 22 22',
            'photo_ok' => true,
        ]);

        $member = Member::where('email', $email)->first();
        $this->assertEquals('Nouveau', $member->first_name);
        $this->assertEquals(1, Member::where('email', $email)->count());
    }

    // ── processExistingNonMember ──

    public function test_process_existing_non_member_sets_date(): void
    {
        Mail::fake();

        $member = $this->makeMember();
        AdhesionService::processExistingNonMember($member);

        $member->refresh();
        $this->assertNotNull($member->membership_requested_at);
        $this->assertEquals(MemberStatus::NonMembre->value, $member->getRawOriginal('statuscode'));
    }

    public function test_process_existing_non_member_sends_invoice(): void
    {
        Mail::fake();

        $member = $this->makeMember();
        AdhesionService::processExistingNonMember($member);

        Mail::assertSent(InvoiceMail::class);
        Mail::assertSent(AdhesionConfirmationMail::class);
    }

    // ── confirmEmail ──

    public function test_confirm_email_sets_status_n_and_date(): void
    {
        Mail::fake();

        $member = $this->makeMember([
            'statuscode' => 'P',
            'activation_token' => 'test',
        ]);

        AdhesionService::confirmEmail($member);

        $member->refresh();
        $this->assertEquals(MemberStatus::NonMembre->value, $member->getRawOriginal('statuscode'));
        $this->assertNotNull($member->membership_requested_at);
        $this->assertNull($member->activation_token);
    }

    public function test_confirm_email_sends_invoice_and_confirmation(): void
    {
        Mail::fake();

        $member = $this->makeMember([
            'statuscode' => 'P',
            'activation_token' => 'test',
        ]);

        AdhesionService::confirmEmail($member);

        Mail::assertSent(InvoiceMail::class);
        Mail::assertSent(AdhesionConfirmationMail::class);
    }
}
