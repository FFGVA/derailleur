<?php

namespace Tests\Feature\Api;

use App\Mail\AdhesionMail;
use App\Models\Member;
use App\Models\MemberPhone;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdhesionEndpointTest extends TestCase
{
    use DatabaseTransactions;

    private array $headers = [
        'Origin' => 'https://ffgva.ch',
        'Content-Type' => 'application/json',
    ];

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'nom' => 'Dupont',
            'prenom' => 'Jean',
            'email' => 'adhesion-test-' . uniqid() . '@example.com',
            'telephone' => '+41 79 123 45 67',
            'photo_ok' => 'oui',
            'type_velo' => 'route',
            'sorties' => 'weekend',
            'atelier' => 'non',
            'instagram' => '@jeandupont',
            'strava' => 'jean_dupont',
            'statuts_ok' => 'oui',
            'cotisation_ok' => 'oui',
        ], $overrides);
    }

    public function test_adhesion_with_valid_data_returns_ok(): void
    {
        Mail::fake();

        $payload = $this->validPayload();
        $response = $this->postJson('/api/adhesion', $payload, $this->headers);

        $response->assertOk()
            ->assertJson(['ok' => true]);
    }

    public function test_member_is_created_in_database_with_correct_data(): void
    {
        Mail::fake();

        $payload = $this->validPayload(['email' => 'adhesion-db-test@example.com']);
        $this->postJson('/api/adhesion', $payload, $this->headers);

        $this->assertDatabaseHas('members', [
            'first_name' => 'Jean',
            'last_name' => 'Dupont',
            'email' => 'adhesion-db-test@example.com',
            'is_invitee' => false,
        ]);
    }

    public function test_member_phone_is_created(): void
    {
        Mail::fake();

        $payload = $this->validPayload(['email' => 'adhesion-phone-test@example.com']);
        $this->postJson('/api/adhesion', $payload, $this->headers);

        $member = Member::where('email', 'adhesion-phone-test@example.com')->first();
        $this->assertNotNull($member);

        $this->assertDatabaseHas('member_phones', [
            'member_id' => $member->id,
            'phone_number' => '+41 79 123 45 67',
            'label' => 'mobile',
        ]);
    }

    public function test_metadata_json_is_populated_with_optional_fields(): void
    {
        Mail::fake();

        $payload = $this->validPayload(['email' => 'adhesion-meta-test@example.com']);
        $this->postJson('/api/adhesion', $payload, $this->headers);

        $member = Member::where('email', 'adhesion-meta-test@example.com')->first();
        $this->assertNotNull($member);
        $this->assertIsArray($member->metadata);
        $this->assertEquals('route', $member->metadata['type_velo']);
        $this->assertEquals('weekend', $member->metadata['sorties']);
        $this->assertEquals('@jeandupont', $member->metadata['instagram']);
        $this->assertEquals('jean_dupont', $member->metadata['strava']);
        $this->assertEquals('oui', $member->metadata['statuts_ok']);
        $this->assertEquals('oui', $member->metadata['cotisation_ok']);
        $this->assertEquals('oui', $member->metadata['photo_ok']);
    }

    public function test_member_statuscode_is_p(): void
    {
        Mail::fake();

        $payload = $this->validPayload(['email' => 'adhesion-status-test@example.com']);
        $this->postJson('/api/adhesion', $payload, $this->headers);

        $member = Member::where('email', 'adhesion-status-test@example.com')->first();
        $this->assertNotNull($member);
        $this->assertEquals('P', $member->getRawOriginal('statuscode'));
    }

    public function test_email_is_sent(): void
    {
        Mail::fake();

        $payload = $this->validPayload();
        $this->postJson('/api/adhesion', $payload, $this->headers);

        Mail::assertSent(AdhesionMail::class, function (AdhesionMail $mail) use ($payload) {
            return $mail->nom === $payload['nom']
                && $mail->prenom === $payload['prenom']
                && $mail->email === $payload['email'];
        });
    }

    public function test_duplicate_email_does_not_create_second_member(): void
    {
        Mail::fake();

        $email = 'adhesion-dup-test@example.com';

        // Create the first member via the endpoint
        $this->postJson('/api/adhesion', $this->validPayload(['email' => $email]), $this->headers);
        $countAfterFirst = Member::where('email', $email)->count();
        $this->assertEquals(1, $countAfterFirst);

        // Submit again with the same email
        $response = $this->postJson('/api/adhesion', $this->validPayload(['email' => $email]), $this->headers);
        $response->assertOk()->assertJson(['ok' => true]);

        $countAfterSecond = Member::where('email', $email)->count();
        $this->assertEquals(1, $countAfterSecond);
    }

    public function test_honeypot_works(): void
    {
        Mail::fake();

        $payload = $this->validPayload(['website' => 'https://spam.bot']);
        $response = $this->postJson('/api/adhesion', $payload, $this->headers);

        $response->assertOk()->assertJson(['ok' => true]);

        Mail::assertNotSent(AdhesionMail::class);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('missingRequiredFieldsProvider')]
    public function test_validation_missing_required_fields(string $field): void
    {
        Mail::fake();

        $payload = $this->validPayload([$field => '']);
        $response = $this->postJson('/api/adhesion', $payload, $this->headers);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors([$field]);
    }

    public static function missingRequiredFieldsProvider(): array
    {
        return [
            'missing nom' => ['nom'],
            'missing prenom' => ['prenom'],
            'missing email' => ['email'],
            'missing telephone' => ['telephone'],
            'missing photo_ok' => ['photo_ok'],
        ];
    }
}
