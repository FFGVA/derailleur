<?php

namespace Tests\Feature;

use App\Mail\AdhesionConfirmationMail;
use App\Models\Member;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdhesionActivationTest extends TestCase
{
    use DatabaseTransactions;

    private function createMemberWithToken(array $overrides = []): array
    {
        $rawToken = Str::random(64);

        $member = Member::create(array_merge([
            'first_name' => 'Marie',
            'last_name' => 'Dupont',
            'email' => 'activation-test-' . uniqid() . '@example.com',
            'statuscode' => 'P',
            'is_invitee' => false,
            'activation_token' => Hash::make($rawToken),
            'activation_sent_at' => now(),
        ], $overrides));

        return [$member, $rawToken];
    }

    public function test_valid_token_confirms_email(): void
    {
        [$member, $rawToken] = $this->createMemberWithToken();

        $response = $this->get('/adhesion/confirmer?' . http_build_query([
            'token' => $rawToken,
            'email' => $member->email,
        ]));

        $response->assertOk();

        $member->refresh();
        $this->assertNotNull($member->email_verified_at);
    }

    public function test_confirmation_email_sent(): void
    {
        Mail::fake();

        [$member, $rawToken] = $this->createMemberWithToken();

        $this->get('/adhesion/confirmer?' . http_build_query([
            'token' => $rawToken,
            'email' => $member->email,
        ]));

        Mail::assertSent(AdhesionConfirmationMail::class, function (AdhesionConfirmationMail $mail) use ($member) {
            return $mail->hasTo($member->email);
        });
    }

    public function test_activation_token_cleared(): void
    {
        [$member, $rawToken] = $this->createMemberWithToken();

        // Verify the token was stored before activation
        $member->refresh();
        $this->assertNotNull($member->activation_token, 'activation_token should be set before confirmation');

        $this->get('/adhesion/confirmer?' . http_build_query([
            'token' => $rawToken,
            'email' => $member->email,
        ]));

        $member->refresh();
        $this->assertNull($member->activation_token);
    }

    public function test_invalid_token_shows_error(): void
    {
        [$member, $rawToken] = $this->createMemberWithToken();

        $response = $this->get('/adhesion/confirmer?' . http_build_query([
            'token' => 'wrong-token-value',
            'email' => $member->email,
        ]));

        $response->assertSee('invalide');
    }

    public function test_expired_token_shows_error(): void
    {
        [$member, $rawToken] = $this->createMemberWithToken([
            'activation_sent_at' => now()->subDays(4),
        ]);

        $response = $this->get('/adhesion/confirmer?' . http_build_query([
            'token' => $rawToken,
            'email' => $member->email,
        ]));

        $response->assertSee('expiré');
    }

    public function test_already_confirmed_shows_error(): void
    {
        [$member, $rawToken] = $this->createMemberWithToken([
            'email_verified_at' => now(),
        ]);

        $response = $this->get('/adhesion/confirmer?' . http_build_query([
            'token' => $rawToken,
            'email' => $member->email,
        ]));

        $response->assertSee('déjà');
    }

    public function test_unknown_email_shows_error(): void
    {
        $response = $this->get('/adhesion/confirmer?' . http_build_query([
            'token' => 'some-token',
            'email' => 'nonexistent-' . uniqid() . '@example.com',
        ]));

        $response->assertSee('invalide');
    }
}
