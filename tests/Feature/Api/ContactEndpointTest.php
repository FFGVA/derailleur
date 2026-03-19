<?php

namespace Tests\Feature\Api;

use App\Mail\ContactMail;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactEndpointTest extends TestCase
{
    use DatabaseTransactions;

    private array $headers = [
        'Origin' => 'https://ffgva.ch',
        'Content-Type' => 'application/json',
    ];

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Jean Dupont',
            'email' => 'jean@example.com',
            'message' => 'Bonjour, je souhaite avoir des informations.',
        ], $overrides);
    }

    public function test_contact_with_valid_data_returns_ok(): void
    {
        Mail::fake();

        $response = $this->postJson('/api/contact', $this->validPayload(), $this->headers);

        $response->assertOk()
            ->assertJson(['ok' => true]);
    }

    public function test_contact_sends_email(): void
    {
        Mail::fake();

        $this->postJson('/api/contact', $this->validPayload(), $this->headers);

        Mail::assertSent(ContactMail::class, function (ContactMail $mail) {
            return $mail->name === 'Jean Dupont'
                && $mail->email === 'jean@example.com'
                && $mail->userMessage === 'Bonjour, je souhaite avoir des informations.';
        });
    }

    public function test_validation_missing_name_returns_422(): void
    {
        Mail::fake();

        $response = $this->postJson('/api/contact', $this->validPayload(['name' => '']), $this->headers);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_validation_missing_email_returns_422(): void
    {
        Mail::fake();

        $response = $this->postJson('/api/contact', $this->validPayload(['email' => '']), $this->headers);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_validation_missing_message_returns_422(): void
    {
        Mail::fake();

        $response = $this->postJson('/api/contact', $this->validPayload(['message' => '']), $this->headers);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['message']);
    }

    public function test_validation_invalid_email_returns_422(): void
    {
        Mail::fake();

        $response = $this->postJson('/api/contact', $this->validPayload(['email' => 'not-an-email']), $this->headers);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_honeypot_filled_website_returns_ok_but_no_email_sent(): void
    {
        Mail::fake();

        $response = $this->postJson('/api/contact', $this->validPayload(['website' => 'https://spam.com']), $this->headers);

        $response->assertOk()
            ->assertJson(['ok' => true]);

        Mail::assertNotSent(ContactMail::class);
    }

    public function test_get_method_returns_405(): void
    {
        $response = $this->getJson('/api/contact', $this->headers);

        $response->assertMethodNotAllowed();
    }

    public function test_cors_request_from_ffgva_gets_proper_headers(): void
    {
        Mail::fake();

        $response = $this->postJson('/api/contact', $this->validPayload(), $this->headers);

        $response->assertHeader('Access-Control-Allow-Origin', 'https://ffgva.ch');
    }
}
