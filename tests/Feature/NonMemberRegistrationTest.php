<?php

namespace Tests\Feature;

use App\Mail\PortalMagicLinkMail;
use App\Models\Member;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class NonMemberRegistrationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_registration_form_is_accessible(): void
    {
        $response = $this->get('/register');

        $response->assertOk();
        $response->assertSee('Créer un compte');
    }

    public function test_registration_creates_non_member_and_sends_magic_link(): void
    {
        Mail::fake();

        $email = 'register-' . uniqid() . '@test.ch';

        $response = $this->post('/register', [
            'prenom' => 'Julie',
            'nom' => 'Test',
            'email' => $email,
            'telephone' => '+41 79 123 45 67',
        ]);

        $response->assertOk();
        $response->assertSee('Vérifie ta boîte mail');

        $member = Member::where('email', $email)->first();
        $this->assertNotNull($member);
        $this->assertEquals('N', $member->getRawOriginal('statuscode'));
        $this->assertEquals('Julie', $member->first_name);

        Mail::assertSent(PortalMagicLinkMail::class, fn ($mail) => $mail->hasTo($email));
    }

    public function test_registration_does_not_duplicate_existing_member(): void
    {
        Mail::fake();

        $email = 'existing-' . uniqid() . '@test.ch';
        Member::create([
            'first_name' => 'Existing',
            'last_name' => 'Member',
            'email' => $email,
            'statuscode' => 'A',
        ]);

        $response = $this->post('/register', [
            'prenom' => 'Julie',
            'nom' => 'Test',
            'email' => $email,
            'telephone' => '+41 79 123 45 67',
        ]);

        // Shows same confirmation page (don't reveal if email exists)
        $response->assertOk();
        $response->assertSee('Vérifie ta boîte mail');

        // Still only one member with that email
        $this->assertEquals(1, Member::where('email', $email)->count());

        // Magic link sent to existing member
        Mail::assertSent(PortalMagicLinkMail::class);
    }

    public function test_honeypot_blocks_bots(): void
    {
        Mail::fake();

        $response = $this->post('/register', [
            'prenom' => 'Bot',
            'nom' => 'Spam',
            'email' => 'bot@spam.com',
            'telephone' => '+41 79 000 00 00',
            'website' => 'https://spam.bot',
        ]);

        $response->assertOk();
        Mail::assertNothingSent();
        $this->assertNull(Member::where('email', 'bot@spam.com')->first());
    }

    public function test_validation_requires_fields(): void
    {
        $response = $this->post('/register', []);

        $response->assertSessionHasErrors(['prenom', 'nom', 'email', 'telephone']);
    }
}
