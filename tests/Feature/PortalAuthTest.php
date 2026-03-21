<?php

namespace Tests\Feature;

use App\Mail\PortalMagicLinkMail;
use App\Models\Member;
use App\Models\MemberMagicToken;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PortalAuthTest extends TestCase
{
    use DatabaseTransactions;

    private function createMember(string $statuscode = 'A'): Member
    {
        return Member::create([
            'first_name' => 'Marie',
            'last_name' => 'Portal',
            'email' => 'portal-test-' . uniqid() . '@example.com',
            'statuscode' => $statuscode,
            'is_invitee' => false,
        ]);
    }

    public function test_login_page_renders(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
        $response->assertSee('Recevoir un lien de connexion');
    }

    public function test_send_link_with_active_member_sends_mail(): void
    {
        Mail::fake();
        $member = $this->createMember('A');

        $this->post('/auth/send-link', ['email' => $member->email]);

        Mail::assertSent(PortalMagicLinkMail::class, function ($mail) use ($member) {
            return $mail->member->id === $member->id;
        });
    }

    public function test_send_link_with_pending_member_sends_mail(): void
    {
        Mail::fake();
        $member = $this->createMember('P');

        $this->post('/auth/send-link', ['email' => $member->email]);

        Mail::assertSent(PortalMagicLinkMail::class);
    }

    public function test_send_link_with_inactive_member_does_not_send_mail(): void
    {
        Mail::fake();
        $member = $this->createMember('I');

        $this->post('/auth/send-link', ['email' => $member->email]);

        Mail::assertNothingSent();
    }

    public function test_send_link_with_draft_member_does_not_send_mail(): void
    {
        Mail::fake();
        $member = $this->createMember('D');

        $this->post('/auth/send-link', ['email' => $member->email]);

        Mail::assertNothingSent();
    }

    public function test_send_link_with_unknown_email_shows_same_confirmation(): void
    {
        Mail::fake();

        $response = $this->post('/auth/send-link', ['email' => 'nobody-' . uniqid() . '@example.com']);

        $response->assertRedirect('/login');
        $response->assertSessionHas('magic_link_success');
        Mail::assertNothingSent();
    }

    public function test_send_link_creates_token_in_database(): void
    {
        Mail::fake();
        $member = $this->createMember('A');

        $this->post('/auth/send-link', ['email' => $member->email]);

        $this->assertDatabaseHas('member_magic_tokens', [
            'member_id' => $member->id,
        ]);
    }

    public function test_send_link_validates_email_required(): void
    {
        $response = $this->post('/auth/send-link', ['email' => '']);

        $response->assertSessionHasErrors('email');
    }

    public function test_send_link_validates_email_format(): void
    {
        $response = $this->post('/auth/send-link', ['email' => 'not-an-email']);

        $response->assertSessionHasErrors('email');
    }

    public function test_valid_token_authenticates_and_redirects_to_portail(): void
    {
        $member = $this->createMember('A');
        [$token, $rawToken] = MemberMagicToken::generateFor($member);

        $response = $this->get('/auth/verify/' . $rawToken);

        $response->assertRedirect('/portail');
    }

    public function test_valid_token_sets_session(): void
    {
        $member = $this->createMember('A');
        [$token, $rawToken] = MemberMagicToken::generateFor($member);

        $this->get('/auth/verify/' . $rawToken);

        $this->assertEquals($member->id, session('portal_member_id'));
        $this->assertNotNull(session('portal_last_activity'));
    }

    public function test_token_marked_used_after_verification(): void
    {
        $member = $this->createMember('A');
        [$token, $rawToken] = MemberMagicToken::generateFor($member);

        $this->get('/auth/verify/' . $rawToken);

        $token->refresh();
        $this->assertNotNull($token->used_at);
    }

    public function test_expired_token_redirects_with_error(): void
    {
        $member = $this->createMember('A');
        [$token, $rawToken] = MemberMagicToken::generateFor($member);
        $token->update(['expires_at' => now()->subMinute()]);

        $response = $this->get('/auth/verify/' . $rawToken);

        $response->assertRedirect('/login');
        $response->assertSessionHas('magic_link_error');
    }

    public function test_used_token_redirects_with_error(): void
    {
        $member = $this->createMember('A');
        [$token, $rawToken] = MemberMagicToken::generateFor($member);
        $token->markUsed();

        $response = $this->get('/auth/verify/' . $rawToken);

        $response->assertRedirect('/login');
        $response->assertSessionHas('magic_link_error');
    }

    public function test_invalid_token_redirects_with_error(): void
    {
        $response = $this->get('/auth/verify/' . str_repeat('a', 64));

        $response->assertRedirect('/login');
        $response->assertSessionHas('magic_link_error');
    }

    public function test_verify_token_rejects_inactive_member(): void
    {
        $member = $this->createMember('I');
        [$token, $rawToken] = MemberMagicToken::generateFor($member);

        $response = $this->get('/auth/verify/' . $rawToken);

        $response->assertRedirect('/login');
        $response->assertSessionHas('magic_link_error');
    }

    public function test_logout_clears_session(): void
    {
        $member = $this->createMember('A');

        $response = $this->withSession([
            'portal_member_id' => $member->id,
            'portal_last_activity' => now()->timestamp,
        ])->post('/deconnexion');

        $response->assertRedirect('/login');
    }

    public function test_login_page_redirects_if_already_authenticated(): void
    {
        $member = $this->createMember('A');

        $response = $this->withSession([
            'portal_member_id' => $member->id,
            'portal_last_activity' => now()->timestamp,
        ])->get('/login');

        $response->assertRedirect('/portail');
    }
}
